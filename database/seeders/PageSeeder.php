<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::truncate();

        $pages = [
            [
                'title' => 'About NovaMart',
                'slug' => 'about-us',
                'meta_title' => 'About NovaMart | India\'s Premier Technology & Lifestyle Marketplace',
                'meta_description' => 'Discover NovaMart, the authorized Indian marketplace for flagship smartphones, creator workstations, audio gear, and lifestyle fashion.',
                'content' => '<h2>Welcome to NovaMart</h2>
<p class="lead">NovaMart is India\'s fastest growing premium marketplace dedicated to authentic consumer electronics, flagship computing, lifestyle fashion, and smart living appliances.</p>
<h3>Our Commitment to Authenticity</h3>
<p>Every product cataloged on NovaMart is guaranteed 100% genuine, brand-sealed, and sourced directly through authorized national distribution channels with official manufacturer warranties across India.</p>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6">
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-brand-600 dark:text-brand-400 mb-1">⚡ 1-Day Delivery</h4>
        <p class="text-xs text-slate-500">Express dispatch to 19,000+ pincodes across all 36 Indian States and Union Territories.</p>
    </div>
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-brand-600 dark:text-brand-400 mb-1">🔒 Bank-Grade Security</h4>
        <p class="text-xs text-slate-500">Razorpay 256-bit SSL encrypted transactions supporting UPI, Rupay, Visa, Mastercard & NetBanking.</p>
    </div>
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-brand-600 dark:text-brand-400 mb-1">🛡️ 7-Day Replacement</h4>
        <p class="text-xs text-slate-500">Hassle-free replacement policy with instant doorstep verification and pickup.</p>
    </div>
</div>
<h3>Corporate Headquarters</h3>
<p>NovaMart Retail Pvt. Ltd.<br>Tower 4, Horizon Tech Hub, SG Highway, Bodakdev,<br>Ahmedabad, Gujarat 380054, India.</p>',
                'is_published' => true,
                'show_in_header' => true,
                'show_in_footer' => true,
                'footer_column' => 'company',
                'sort_order' => 1,
            ],
            [
                'title' => 'Contact & Customer Helpline',
                'slug' => 'contact-us',
                'meta_title' => 'Contact Us & 24/7 Helpline | NovaMart Customer Support',
                'meta_description' => 'Get in touch with NovaMart customer care. Reach our dedicated support team via toll-free helpline, WhatsApp or email.',
                'content' => '<h2>Customer Care & Support</h2>
<p>We are available 7 days a week, 9:00 AM to 9:00 PM IST to assist you with order status, technical inquiries, product warranties, and returns.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
    <div class="p-5 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-slate-900 dark:text-white mb-2">📞 Helpline Phone</h4>
        <p class="text-sm font-mono text-brand-600 dark:text-brand-400 font-bold">+91 8000 999 888</p>
        <p class="text-xs text-slate-400 mt-1">Toll-free across India (Mon - Sun, 9am - 9pm)</p>
    </div>
    <div class="p-5 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-slate-900 dark:text-white mb-2">✉️ Email Support</h4>
        <p class="text-sm font-mono text-brand-600 dark:text-brand-400 font-bold">support@novamart.in</p>
        <p class="text-xs text-slate-400 mt-1">Average response time under 2 hours</p>
    </div>
</div>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'help',
                'sort_order' => 2,
            ],
            [
                'title' => 'Shipping & Delivery Policy',
                'slug' => 'shipping-policy',
                'meta_title' => 'Shipping & Delivery Policy | NovaMart India',
                'meta_description' => 'Learn about NovaMart shipping timelines, free delivery threshold, tracking, and nationwide coverage.',
                'content' => '<h2>Shipping & Delivery Guidelines</h2>
<p>NovaMart delivers across all 28 states and 8 union territories of India through premier logistics partners including BlueDart, Delhivery, and Xpressbees.</p>
<h3>Shipping Charges</h3>
<ul>
    <li><strong>Orders Above ₹499:</strong> FREE standard delivery across India.</li>
    <li><strong>Orders Under ₹499:</strong> Nominal flat fee of ₹49.</li>
    <li><strong>Express 1-Day Air Delivery:</strong> ₹99 available in select metro zones.</li>
</ul>
<h3>Delivery Timelines</h3>
<ul>
    <li><strong>Metro Cities (Delhi NCR, Mumbai, Bengaluru, Ahmedabad, Hyderabad, Chennai, Kolkata):</strong> 1 - 2 business days.</li>
    <li><strong>Tier 2 & Tier 3 Cities:</strong> 2 - 4 business days.</li>
    <li><strong>Remote Regions:</strong> 4 - 6 business days.</li>
</ul>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'help',
                'sort_order' => 3,
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'refund-policy',
                'meta_title' => '7-Day Return & Replacement Policy | NovaMart',
                'meta_description' => 'Understand NovaMart 7-day hassle-free returns, replacement conditions, and refund timelines.',
                'content' => '<h2>7-Day Easy Replacement Policy</h2>
<p>At NovaMart, customer satisfaction is paramount. If you receive a damaged, defective, or incorrect product, you are eligible for an immediate replacement or full refund within 7 days of delivery.</p>
<h3>Return Process</h3>
<ol>
    <li>Log into your NovaMart account and navigate to <strong>My Orders</strong>.</li>
    <li>Select the order item and click <strong>Request Return / Replacement</strong>.</li>
    <li>Our courier partner will schedule a doorstep quality check and pickup within 24-48 hours.</li>
    <li>Once picked up, your replacement unit will be dispatched immediately or your refund credited back to your original payment method within 3-5 bank working days.</li>
</ol>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'legal',
                'sort_order' => 4,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'meta_title' => 'Terms of Service & User Agreement | NovaMart',
                'meta_description' => 'Read NovaMart terms and conditions governing the use of our marketplace, user accounts, and purchases.',
                'content' => '<h2>Terms of Service</h2>
<p>By accessing or purchasing from NovaMart, you agree to comply with our Terms of Service. All orders placed are subject to product availability and pricing confirmation.</p>
<h3>Pricing & Invoicing</h3>
<p>All prices listed on NovaMart are inclusive of applicable GST (Goods and Services Tax). A formal tax invoice will be generated and emailed to your registered address upon order dispatch.</p>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'legal',
                'sort_order' => 5,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_title' => 'Privacy Policy & Data Security | NovaMart',
                'meta_description' => 'NovaMart privacy policy explains how your personal data, payment info, and addresses are protected.',
                'content' => '<h2>Privacy Policy & Data Protection</h2>
<p>NovaMart is committed to safeguarding your privacy. We never sell or share your personal contact details with third-party advertising brokers.</p>
<h3>Payment Data Security</h3>
<p>We do not store your credit/debit card numbers, CVVs, or UPI PINs. All payment transactions are securely processed directly through RBI-compliant, PCI-DSS Level 1 certified gateways (Razorpay).</p>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'legal',
                'sort_order' => 6,
            ],
            [
                'title' => 'Frequently Asked Questions (FAQ)',
                'slug' => 'faq',
                'meta_title' => 'Frequently Asked Questions | NovaMart Help Center',
                'meta_description' => 'Find quick answers to common questions about orders, warranty, shipping, payments, and returns on NovaMart.',
                'content' => '<h2>Frequently Asked Questions</h2>
<div class="space-y-4 my-6">
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Are all products 100% genuine?</h4>
        <p class="text-xs text-slate-500">Yes. NovaMart only sources directly from authorized Indian brand distributors. All devices come with authentic serial numbers and official manufacturer warranties.</p>
    </div>
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-slate-900 dark:text-white mb-1">How can I track my order?</h4>
        <p class="text-xs text-slate-500">Go to your Account > Orders tab. Live tracking details and courier airway bill (AWB) numbers are updated in real time as your package is dispatched.</p>
    </div>
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Which payment options are supported?</h4>
        <p class="text-xs text-slate-500">We support all Indian payment modes via Razorpay: UPI (Google Pay, PhonePe, Paytm, BHIM), Credit/Debit Cards, NetBanking across 50+ banks, EMI, and Cash on Delivery (COD).</p>
    </div>
</div>',
                'is_published' => true,
                'show_in_header' => false,
                'show_in_footer' => true,
                'footer_column' => 'help',
                'sort_order' => 7,
            ],
        ];

        foreach ($pages as $p) {
            Page::create($p);
        }
    }
}
