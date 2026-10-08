<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [];
        foreach (array_keys(config('store.defaults')) as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $toggles = config('store.toggles');
        $jsonFields = config('store.json');

        $request->validate([
            'store_name' => 'required|string|max:100',
            'store_email' => 'nullable|email|max:150',
            'logo_image_url' => 'nullable|url',
            'site_favicon_url' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'google_redirect_uri' => 'nullable|url',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'standard_shipping_fee' => 'required|numeric|min:0',
            'express_shipping_fee' => 'required|numeric|min:0',
        ]);

        foreach ($jsonFields as $field) {
            if ($request->filled($field) && !is_array(json_decode($request->input($field), true))) {
                return back()->withInput()->with('error', "\"{$field}\" is not valid JSON. Nothing was saved.");
            }
        }

        foreach (array_keys(config('store.defaults')) as $field) {
            if (in_array($field, $toggles, true)) {
                Setting::set($field, $request->boolean($field) ? '1' : '0');
            } elseif ($request->has($field)) {
                Setting::set($field, (string) $request->input($field));
            }
        }

        return back()->with('success', 'Store settings and visual layout configurations updated successfully.');
    }
}
