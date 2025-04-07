@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="alert alert-danger text-center">
            <h1>Insufficient Credit</h1>
            <p>Your current credit balance is not enough to complete this transaction.</p>
            <a href="{{ route('products_list') }}" class="btn btn-primary">OK</a>
        </div>
    </div>
@endsection
