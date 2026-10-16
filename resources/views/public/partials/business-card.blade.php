@php
    $cover = $business->cover ?: $business->logo;
    $coverUrl = $cover ? (\Illuminate\Support\Str::startsWith($cover, ['http://','https://']) ? $cover : asset('storage/' . ltrim($cover, '/'))) : null;
    $location = $business->city?->name ?? $business->address ?? 'Local business';
@endphp
@php $paidPromotion = $paidPromotions[$business->id] ?? null; @endphp
<a class="g-business-card" href="{{ $paidPromotion ? route('growya.promotions.click', $paidPromotion) : route('public.business.home', $business->slug) }}">
    <div class="g-business-cover">@if($paidPromotion)<span class="g-badge" style="position:absolute;z-index:2;right:12px;top:12px">Sponsored</span>@endif@if($coverUrl)<img src="{{ $coverUrl }}" alt="{{ $business->name }}" loading="lazy">@else<div class="g-business-cover-initial">{{ mb_strtoupper(mb_substr($business->name, 0, 2)) }}</div>@endif<span class="g-badge"><x-growya.icon name="shield" style="width:12px;height:12px" />Approved</span></div>
    <div class="g-business-info"><p class="g-kicker">{{ $business->category?->name ?? 'Local business' }}</p><h3>{{ $business->name }}</h3>@if(\App\Support\PublicOfferDisplay::forBusiness($business, 1)->isNotEmpty())<span class="g-badge g-tone-amber" style="margin-top:8px">Active offer</span>@endif<p class="g-business-location"><x-growya.icon name="pin" />{{ \Illuminate\Support\Str::limit($location, 65) }}</p>@if($business->description)<p class="g-business-description">{{ \Illuminate\Support\Str::limit(strip_tags($business->description), 110) }}</p>@endif<div class="g-card-footer"><span>{{ number_format((int) $business->views_count) }} showroom views</span><span class="g-link">Visit showroom<x-growya.icon name="arrow-up-right" /></span></div></div>
</a>
