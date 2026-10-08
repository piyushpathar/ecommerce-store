<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Handle incoming Razorpay Webhook Events
     */
    public function handleRazorpay(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature', '');
        $payload = $request->getContent();

        // Verify webhook signature
        $isValid = $this->razorpayService->verifyWebhookSignature($payload, $signature);

        if (!$isValid) {
            Log::warning('Razorpay Webhook: Invalid signature received', [
                'header' => $signature,
                'ip' => $request->ip()
            ]);
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? 'unknown';

        Log::info("Razorpay Webhook event received: {$event}");

        switch ($event) {
            case 'payment.captured':
            case 'order.paid':
                $payment = $data['payload']['payment']['entity'] ?? [];
                $razorpayOrderId = $payment['order_id'] ?? null;
                $razorpayPaymentId = $payment['id'] ?? null;

                if ($razorpayOrderId) {
                    $order = Order::where('razorpay_order_id', $razorpayOrderId)->first();
                    if ($order) {
                        $history = $order->status_history ?? [];
                        $history[] = [
                            'status' => 'confirmed',
                            'title' => "Payment Captured via Razorpay Webhook ({$event})",
                            'timestamp' => now()->toDateTimeString(),
                        ];

                        $order->update([
                            'payment_status' => 'paid',
                            'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
                            'razorpay_payment_id' => $razorpayPaymentId ?: $order->razorpay_payment_id,
                            'status_history' => $history,
                        ]);
                    }
                }
                break;

            case 'payment.failed':
                $payment = $data['payload']['payment']['entity'] ?? [];
                $razorpayOrderId = $payment['order_id'] ?? null;

                if ($razorpayOrderId) {
                    $order = Order::where('razorpay_order_id', $razorpayOrderId)->first();
                    if ($order) {
                        $history = $order->status_history ?? [];
                        $history[] = [
                            'status' => 'failed',
                            'title' => 'Payment Failed via Webhook: ' . ($payment['error_description'] ?? 'Transaction Declined'),
                            'timestamp' => now()->toDateTimeString(),
                        ];

                        $order->update([
                            'payment_status' => 'failed',
                            'status_history' => $history,
                        ]);
                    }
                }
                break;

            default:
                Log::info("Razorpay Webhook: Unhandled event type {$event}");
                break;
        }

        return response()->json(['status' => 'success', 'event' => $event], 200);
    }
}
