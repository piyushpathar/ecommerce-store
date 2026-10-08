{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('shop.index') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('plans.index') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ route('shop.category', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at ? $category->updated_at->format('Y-m-d') : date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach($products as $product)
    <url>
        <loc>{{ route('shop.product', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at ? $product->updated_at->format('Y-m-d') : date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.85</priority>
    </url>
    @endforeach
</urlset>
