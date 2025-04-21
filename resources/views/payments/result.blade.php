<?php

@section('content')
    <div class="container">
        <h2>Payment Result</h2>

        @if($status === 'success')
            <div class="alert alert-success">Your payment was successful!</div>
        @elseif($status === 'failed')
            <div class="alert alert-danger">Payment failed. Please try again.</div>
        @endif

        @if($errors->has('payment'))
            <div class="alert alert-danger">{{ $errors->first('payment') }}</div>
        @endif

        @if($order)
            <h3>Order Details:</h3>
            <p>Order Number: {{ $order->order_number }}</p>
            <p>Total Amount: ${{ $order->total_amount }}</p>
            <!-- Add any other order details you want to display -->
        @else
            <p>No order found.</p>
        @endif
    </div>
@endsection
