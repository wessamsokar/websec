<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Artisan;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Purchase;

class UsersController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:web')->except('register', 'doRegister', 'login', 'doLogin');
    }
    public function reset(Request $request)
    {

        $user = User::find($request->id);
        $user->credit = 0;

        $user->save();

        return back()->with('success', 'Credit updated successfully!');
    }

    public function purchases()
    {
        $user = auth()->user();
        $purchases = Purchase::where('user_id', $user->id)->with('product')->get();

        return view('products.bought_products_list', compact('purchases'));
    }

    public function insufficientCredit()
    {
        return view('users.insufficient-credit');
    }

    public function updateCredit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'credit' => 'required|numeric|min:0',
        ]);

        $user = User::find($request->id);
        $user->credit = $request->credit;
        $user->save();

        return back()->with('success', 'Credit updated successfully!');
    }

    public function create()
    {

        return view('users.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ], [
            'email.unique' => 'The email has already been taken.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->assignRole("Employee");

        return redirect()->route('users.list')->with('success', 'User created successfully!');
    }

    public function register(Request $request)
    {
        return view('users.register');
    }

    public function doRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('Customer');

        Auth::login($user);

        return redirect('/')->with('success', 'Registration successful!');
    }

    public function list(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }

        $query = User::query();

        if (auth()->user()->hasRole('Employee')) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Customer');
            });
        }
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Admin');
        });

        $query->when(
            $request->keywords,
            fn($q) => $q->where("name", "like", "%$request->keywords%")
        );

        $users = $query->get();
        return view('users.list', compact('users'));
    }



    public function login(Request $request)
    {
        return view('users.login');
    }

    public function doLogin(Request $request)
    {

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]))
            return redirect()->back()->withInput($request->input())->withErrors('Invalid login information.');

        $user = User::where('email', $request->email)->first();
        Auth::setUser($user);

        return redirect('/');
    }

    public function doLogout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function profile(Request $request, User $user = null)
    {
        $user = $user ?? auth()->user();
        if (auth()->id() != $user->id) {
            if (!auth()->user()->hasPermissionTo('show_users'))
                abort(401);
        }

        $permissions = [];
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('users.profile', compact('user', 'permissions'));
    }

    public function edit(Request $request, User $user = null)
    {
        $user = $user ?? auth()->user();
        if (auth()->id() != $user?->id) {
            if (!auth()->user()->hasPermissionTo('edit_users'))
                abort(401);
        }

        $roles = [];
        foreach (Role::all() as $role) {
            $role->taken = ($user->hasRole($role->name));
            $roles[] = $role;
        }

        $permissions = [];
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach (Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user)
    {

        if (auth()->id() != $user->id) {
            if (!auth()->user()->hasPermissionTo('show_users'))
                abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if (auth()->user()->hasPermissionTo('admin_users')) {

            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            Artisan::call('cache:clear');
        }



        return redirect(route('profile', ['user' => $user->id]));
    }

    public function delete(Request $request, User $user)
    {

        if (!auth()->user()->hasPermissionTo('delete_users'))
            abort(401);

        $user->delete();

        return redirect()->route('users.list');
    }

    public function editPassword(Request $request, User $user = null)
    {

        $user = $user ?? auth()->user();
        if (auth()->id() != $user?->id) {
            if (!auth()->user()->hasPermissionTo('edit_users'))
                abort(401);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user)
    {

        if (auth()->id() == $user?->id) {



            if (!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {

                Auth::logout();
                return redirect('/');
            }
        } else if (!auth()->user()->hasPermissionTo('edit_users')) {

            abort(401);
        }

        $user->password = bcrypt($request->password); //Secure
        $user->save();

        return redirect(route('profile', ['user' => $user->id]));
    }


}
