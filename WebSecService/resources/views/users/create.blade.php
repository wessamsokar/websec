@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mb-4">Create User</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('users.list') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
