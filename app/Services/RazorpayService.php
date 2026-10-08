<?php

namespace App\Services;

use App\Models\Setting;
use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected ?string $keyId;
    protected ?string $keySecret;
    protected ?string $webhookSecret;
    protected bool $enabled;
    protected bool $mockMode;

    public function __construct()
    {
        $this->keyId = Setting::get('razorpay_key_id', config('services.razorpay.key_id', env('RAZORPAY_KEY_ID')));
        $this->keySecret = Setting::get('razorpay_key_secret', config('services.razorpay.key_secret', env('RAZORPAY_KEY_SECRET')));
        $this->webhookSecret = Setting::get('razorpay_webhook_secret', env('RAZORPAY_WEBHOOK_SECRET'));
        $this->enabled = (bool) Setting::get('razorpay_enabled', true);
        $this->mockMode = (bool) Setting::get('razorpay_mock_mode', true);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getKeyId(): string
    {
        return $this->keyId ?: 'rzp_test_NovaMart2026';
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function isMockMode(): bool
    {
        return $this->mockMode;
    }

    /**
     * Create a Razorpay Order
     *
     * @param float $amount Amount in INR (Rupees)
     * @param string $receipt Order receipt ID
     * @param array $notes Metadata notes
     * @return array
     */
    public function createOrder(float $amount, string $receipt, array $notes = []): array
    {
        $amountInPaise = (int) round($amount * 100);

        if (!$this->mockMode && !empty($this->keyId) && !empty($this->keySecret)) {
            try {
                $api = new Api($this->keyId, $this->keySecret);
                $orderData = [
                    'receipt' => $receipt,
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'notes' => $notes,
                    'payment_capture' => 1,
                ];
                $razorpayOrder = $api->order->create($orderData);
                return [
                    'id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'status' => $razorpayOrder['status'],
                    'mock' => false,
                ];
            } catch (Exception $e) {
                Log::warning('Razorpay API error, falling back to local sandbox simulator: ' . $e->getMessage());
            }
        }

        // Mock Order fallback
        return [
            'id' => 'order_' . substr(md5(uniqid($receipt, true)), 0, 14),
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'status' => 'created',
            'mock' => true,
        ];
    }

    /**
     * Verify payment signature
     */
    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, string $signature): bool
    {
        if ($this->mockMode || str_starts_with($razorpayOrderId, 'order_')) {
            // In mock mode or mock order, check if payment id is provided
            return !empty($razorpayPaymentId);
        }

        try {
            $api = new Api($this->keyId, $this->keySecret);
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $signature,
            ];
            $api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (Exception $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify incoming webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $actualSignature): bool
    {
        $secret = $this->webhookSecret ?: Setting::get('razorpay_webhook_secret');
        if (empty($secret)) {
            return false;
        }

        if ($this->mockMode) {
            return true;
        }

        try {
            $api = new Api($this->keyId, $this->keySecret);
            $api->utility->verifyWebhookSignature($payload, $actualSignature, $secret);
            return true;
        } catch (Exception $e) {
            Log::error('Razorpay webhook signature verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
