<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(
     *     path="/api/payment/pay/{gateway}",
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

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
        // Check if the order belongs to the authenticated customer
        if ($order->customer_id !== Auth::guard('customer')->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to pay for this order.'
            ], 403);
        }


        try {
            // Process the payment
            $paymentData = $this->paymentService->pay($order, $gateway);

            return response()->json([
                'redirect_url' => $paymentData['redirect_url']
            ]);

            // Redirect to the payment gateway
          //  return redirect()->to($paymentData['redirect_url']);
        } catch (\Throwable $e) {
            // Handle payment errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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
    public function zarinpalCallback2(Request $request)

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


    public function zarinpalCallback(Request $request)
    {
        $authority = $request->input('Authority');
        if (!$authority) {
            return redirect()->route('payment.result')
                ->withErrors(['payment' => 'Missing Authority parameter']);
        }

        $transaction = OrderTransaction::where('transaction_id', $authority)
            ->first();

        if ($transaction) {
            // make sure OrderTransaction has ->order() relation (belongsTo)
            $order = $transaction->order ?? Order::find($transaction->order_id);
        } else {
            return redirect()->route('payment.result')
                ->withErrors(['payment' => 'Transaction not found']);

        }

        if (! $order) {
            return redirect()->route('payment.result')
                ->withErrors(['payment' => 'Order not found']);
        }

        try {
            $success = $this->paymentService->verify($order, 'zarinpal', $request->all());

            return redirect()->route('payment.result', ['order_id' => $order->id])
                ->with(['status'=>$success ? 'success' : 'failed',
                    'order'  => $order,]);

        } catch (\Throwable $e) {
            \Log::error('Zarinpal callback error: '.$e->getMessage(), [
                'request' => $request->all(),
                'order_id' => $order->id ?? null,
            ]);

            return redirect()
                ->route('payment.result')
                ->withErrors(['payment' => $e->getMessage()])
                ->with([
                    'status' => 'failed',
                    'order'  => $order,
                ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/payment/verify",
     *     summary="Verify payment by Authority and gateway",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Authority"},
     *             @OA\Property(
     *                 property="Authority",
     *                 type="string",
     *                 example="A123456789"
     *             ),
     *             @OA\Property(
     *                 property="gateway",
     *                 type="string",
     *                 example="zarinpal",
     *                 description="Payment gateway (default: zarinpal)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification result",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="order_id", type="integer", example=123),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="order", type="object", description="Order details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing Authority parameter",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Missing Authority parameter")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction or Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transaction not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Verification error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Verification error: ..."),
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="order_id", type="integer", nullable=true)
     *         )
     *     )
     * )
     */
    public function Verify(Request $request)
    {
        $authority = $request->input('Authority');
        $gateway = $request->input('gateway', 'zarinpal'); // default to zarinpal

        if (!$authority) {
            return response()->json([
                'success' => false,
                'message' => 'Missing Authority parameter',
            ], 400);
        }

        $transaction = OrderTransaction::where('transaction_id', $authority)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        $order = $transaction->order ?? Order::find($transaction->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        try {
            $success = $this->paymentService->verify($order, $gateway, $request->all());

            return response()->json([
                'success' => $success,
                'order_id' => $order->id,
                'status' => $success ? 'success' : 'failed',
                'order' => $order,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Payment verification error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'order_id' => $order->id ?? null,
                'gateway' => $gateway,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification error: ' . $e->getMessage(),
                'order_id' => $order->id ?? null,
                'status' => 'failed',
            ], 500);
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
