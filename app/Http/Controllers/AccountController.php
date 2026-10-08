<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'orders');

        $orders = Order::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('account.index', compact('user', 'orders', 'tab'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->input('name');
        $user->phone = $request->input('phone');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function addAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:100',
            'address_type' => 'required|string|in:home,work,other',
        ]);

        $addresses = $user->addresses ?? [];
        $isDefault = $request->boolean('is_default') || empty($addresses);

        if ($isDefault) {
            foreach ($addresses as &$addr) {
                $addr['is_default'] = false;
            }
        }

        $addresses[] = [
            'id' => 'addr_' . Str::random(8),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'pincode' => $request->input('pincode'),
            'landmark' => $request->input('landmark'),
            'address_type' => $request->input('address_type'),
            'is_default' => $isDefault,
        ];

        $user->addresses = $addresses;
        $user->save();

        return redirect()->route('account.index', ['tab' => 'addresses'])->with('success', 'New address added.');
    }

    public function deleteAddress(string $id)
    {
        $user = Auth::user();
        $addresses = $user->addresses ?? [];

        $filtered = array_values(array_filter($addresses, fn($a) => ($a['id'] ?? '') !== $id));
        $user->addresses = $filtered;
        $user->save();

        return redirect()->route('account.index', ['tab' => 'addresses'])->with('success', 'Address deleted.');
    }
}
