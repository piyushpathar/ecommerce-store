<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Order;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return view('plans.index', compact('plans'));
    }

    public function subscribe(string $slug, Request $request)
    {
        $plan = Plan::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $user = Auth::user();
        $receipt = 'SUB-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

        // Create Razorpay order
        $rpOrder = $this->razorpayService->createOrder(
            $plan->price,
            $receipt,
            ['plan_id' => (string) $plan->id, 'user_id' => $user ? (string) $user->id : 'guest']
        );

        return view('plans.checkout', [
            'plan' => $plan,
            'razorpayOrder' => $rpOrder,
            'razorpayKey' => $this->razorpayService->getKeyId(),
            'isMock' => $this->razorpayService->isMockMode(),
        ]);
    }
}
