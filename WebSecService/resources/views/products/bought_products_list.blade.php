@extends('layouts.master')

@section('content')
    <div class="container">
        <h1>Bought Products</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Purchased At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->product->name }}</td>
                        <td>{{ $purchase->quantity }}</td>
                        <td>{{ $purchase->total_price }}</td>
                        <td>{{ $purchase->purchased_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
