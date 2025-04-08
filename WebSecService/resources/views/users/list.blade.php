@extends('layouts.master')
@section('title', 'Users')
@section('content')
    <div class="row mt-2">
        <div class="col col-10">
            <h1>Users</h1>
        </div>
    </div>
    <form>
        <div class="row">
            <div class="col col-sm-2">
                <input name="keywords" type="text" class="form-control" placeholder="Search Keywords"
                    value="{{ request()->keywords }}" />
            </div>
            <div class="col col-sm-1">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <div class="col col-sm-1">
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>
        </div>
    </form>

    <div class="card mt-2">
        <div class="card-body">
            <table class="table">
                @can('admin_users')
                    <a href="{{ route('users.create') }}" class="btn btn-success mb-4">Create New User</a>
                @endcan
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Roles</th>
                        @can('edit_customer')
                            <th scope="col">Credit</th>
                        @endcan
                        @can('edit_users' && 'delete_users')
                            <th scope="col">Actions</th>
                            <th scope="col">Actions</th>
                        @endcan
                    </tr>
                </thead>
                @foreach($users as $user)
                    <tr>
                        <td scope="col">{{$user->id}}</td>
                        <td scope="col">{{$user->name}}</td>
                        <td scope="col">{{$user->email}}</td>
                        <td scope="col">
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{$role->name}}</span>
                            @endforeach
                        </td>
                        @can('edit_customer')
                            <td scope="col">
                                <form action="{{ route('update.credit') }}" method="POST">
                                    @csrf
                                    <input type="number" name="credit" class="form-control" value="{{ $user->credit }}"
                                        min="{{ $user->credit }}">
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </form>

                                <form action="{{ route('reset.credit') }}" method="POST">
                                    @csrf
                                    @can('edit_customer')
                                    <input type="hidden" name="credit" class="form-control" value="{{ $user->credit }}"
                                        min="{{ $user->credit }}">
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-primary mt-2">Reset</button>
                                    @endcan
                                </form>

                            </td>
                        @endcan

                        <td scope="col">
                            @can('edit_users')
                                <a class="btn btn-primary" href='{{route('users_edit', [$user->id])}}'>Edit</a>
                            @endcan
                            @can('admin_users')
                                <a class="btn btn-primary" href='{{route('edit_password', [$user->id])}}'>Change Password</a>
                            @endcan
                            @can('delete_users')
                                <a class="btn btn-danger" href='{{route('users_delete', [$user->id])}}'>Delete</a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>


@endsection
