<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(
     *     path="/payment/pay/{gateway}",
     *     summary="Initiate a payment",
     *     description="Redirects user to the payment gateway for the given order.",
     *     operationId="pay",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="gateway",
     *         in="path",
     *         required=true,
     *         description="Payment gateway (e.g., idpay, zarinpal)",
     *         @OA\Schema(type="string", example="zarinpal")
     *     ),
     *     @OA\Parameter(
     *         name="order_number",
     *         in="query",
     *         required=true,
     *         description="The order number to initiate payment for",
     *         @OA\Schema(type="string", example="ORD123456789")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to the selected gateway"
     *     )
     * )
     */

    public function pay(Request $request, string $gateway)
    {
        // Find the order by order number
        $order = Order::where('order_number', $request->order_number)->firstOrFail();

        // Check if the order belongs to the authenticated customer
        if ($order->customer_id !== auth('customer')->id()) {
            return redirect()->back()->withErrors(['payment' => 'You do not have permission to pay for this order.']);
        }

        try {
            // Process the payment
            $paymentData = $this->paymentService->pay($order, $gateway);

            // Redirect to the payment gateway
            return redirect()->to($paymentData['redirect_url']);
        } catch (\Throwable $e) {
            // Handle payment errors
            return redirect()->back()->withErrors(['payment' => $e->getMessage()]);
        }
    }


    /**
     * @OA\Get(
     *     path="/payment/idpay/callback",
     *     summary="Handle IDPay callback",
     *     description="Handles the payment verification callback from IDPay.",
     *     operationId="idpayCallback",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="query",
     *         required=true,
     *         description="The order number associated with the payment",
     *         @OA\Schema(type="string", example="ORD123456789")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirects to payment result page"
     *     )
     * )
     */
    public function idpayCallback(Request $request)

    {
        $order = Order::where('order_number', $request->order_id)->firstOrFail();

        try {
            $success = $this->paymentService->verify($order, 'idpay', $request->all());
            return redirect()->route('payment.result')->with([
                'status' => $success ? 'success' : 'failed',
                'order' => $order,  // Pass the order data to the result page
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('payment.result')->withErrors(['payment' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment/zarinpal/callback",
     *     summary="Handle Zarinpal callback",
     *     description="Handles the payment verification callback from Zarinpal.",
     *     operationId="zarinpalCallback",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="Authority",
     *         in="query",
     *         required=true,
     *         description="Zarinpal authority code used to find the order",
     *         @OA\Schema(type="string", example="A00000123456789")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirects to payment result page"
     *     )
     * )
     */
    public function zarinpalCallback(Request $request)

    {
        $order = Order::where('order_number', $request->Authority)->first(); // Adjust if needed to find order by Authority

        if (!$order) {
            return redirect()->route('payment.result')->withErrors(['payment' => 'Order not found']);
        }

        try {
            $success = $this->paymentService->verify($order, 'zarinpal', $request->all());
            return redirect()->route('payment.result')->with([
                'status' => $success ? 'success' : 'failed',
                'order' => $order,  // Pass the order data to the result page
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('payment.result')->withErrors(['payment' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment/result",
     *     summary="Show payment result",
     *     description="Displays the result of a payment, including success or failure message.",
     *     operationId="paymentResult",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="Returns the payment result view"
     *     )
     * )
     */
    public function result()
    {
        // Retrieve the order from the session
        $order = session('order');
        $status = session('status');

        return view('payments.result', compact('order', 'status')); // Pass both order and status to the view
    }
}
