@extends('layouts.master')
@section('title', 'Edit User')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#clean_permissions").click(function () {
                $('input[name="permissions[]"]').prop('checked', false);
            });
            $("#clean_roles").click(function () {
                $('input[name="roles[]"]').prop('checked', false);
            });
        });
    </script>
    <div class="d-flex justify-content-center">
        <div class="row m-4 col-sm-8">
            <form action="{{route('users_save', $user->id)}}" method="post">
                {{ csrf_field() }}
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">
                        <strong>Error!</strong> {{$error}}
                    </div>
                @endforeach
                <div class="row mb-2">
                    <div class="col-12">
                        <label for="code" class="form-label">Name:</label>
                        <input type="text" class="form-control" placeholder="Name" name="name" required
                            value="{{$user->name}}">
                    </div>
                </div>
                @can('admin_users')
                    <div class="col-12 mb-2">
                        <label for="model" class="form-label">Roles:</label>
                        (<a href='#' id='clean_roles'>reset</a>)
                        <div class="d-flex flex-column">
                            @foreach($roles as $role)
                                <div class="d-flex align-items-center mb-2">
                                    <input type="checkbox" class="form-check-input me-2" name="roles[]" value="{{$role->name}}"
                                        {{$role->taken ? 'checked' : ''}}>
                                    <label class="form-check-label me-2">{{$role->name}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="model" class="form-label">Direct Permissions:</label>
                        (<a href='#' id='clean_permissions'>reset</a>)
                        <div class="d-flex flex-column">
                            @foreach($permissions as $permission)
                                <div class="d-flex align-items-center mb-2">
                                    <input type="checkbox" class="form-check-input me-2" name="permissions[]"
                                        value="{{$permission->name}}" {{$permission->taken ? 'checked' : ''}}>
                                    <label class="form-check-label me-2">{{$permission->display_name}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endcan

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
