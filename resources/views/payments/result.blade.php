<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Payment Result</h2>

    @if(session('status') === 'success')
        <div class="alert alert-success">Your payment was successful!</div>
    @elseif(session('status') === 'failed')
        <div class="alert alert-danger">Payment failed. Please try again.</div>
    @endif

    @if($errors->has('payment'))
        <div class="alert alert-danger">{{ $errors->first('payment') }}</div>
    @endif

    @if(session('order'))
        @php
            $order = session('order');
        @endphp
        <h3>Order Details:</h3>
        <p>Order Number: {{ $order->order_number }}</p>
        <p>Total Amount: ${{ $order->total }}</p>
    @else
        <p>No order found.</p>
    @endif
</div>
</body>
</html>
