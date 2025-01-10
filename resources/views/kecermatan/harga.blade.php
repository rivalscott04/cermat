@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Pricing Toggle -->
    <div class="text-center mb-5">
        <div class="pricing-toggle">
            <button class="active">ANNUALLY <span class="badge save-badge">SAVE 20%</span></button>
            <button class="text-muted">MONTHLY</button>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($plans as $plan)
            <div class="col">
                <div class="card h-100 plan-card">
                    @if($plan['is_popular'])
                        <div class="most-popular">Most popular</div>
                    @endif
                    <div class="card-body">
                        <h5 class="text-center plan-title mb-4">{{ $plan['name'] }}</h5>
                        <div class="text-center mb-4">
                            <span class="plan-price">{{ $plan['price'] }}</span>
                            <span class="price-currency">$</span>
                            <div class="price-period">per user / month</div>
                        </div>
                        <p class="card-description text-center">{{ $plan['description'] }}</p>
                        <ul class="list-unstyled feature-list">
                            @foreach($plan['features'] as $feature)
                                <li>
                                    <span class="feature-check">✓</span> {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            <button class="btn btn-primary action-button">{{ $plan['button_text'] }}</button>
                            @if($plan['trial_text'])
                                <div class="text-center trial-text">{{ $plan['trial_text'] }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    .plan-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: transform 0.3s;
    }
    .plan-card:hover {
        transform: translateY(-5px);
    }
    .plan-title {
        color: #666;
        font-size: 1rem;
        letter-spacing: 1px;
    }
    .plan-price {
        font-size: 4rem;
        color: #1ab394;
        font-weight: 500;
        line-height: 1;
    }
    .price-currency {
        font-size: 1.5rem;
        position: relative;
        top: -30px;
        color: #1ab394;
    }
    .price-period {
        color: #666;
        font-size: 0.9rem;
    }
    .pricing-toggle {
        background: #f1f3f9;
        border-radius: 30px;
        padding: 4px;
        display: inline-flex;
        margin-bottom: 2rem;
    }
    .pricing-toggle button {
        border-radius: 25px;
        padding: 8px 24px;
        border: none;
        background: transparent;
        font-weight: 500;
    }
    .pricing-toggle .active {
        background: #1ab394;
        color: white;
    }
    .save-badge {
        background: #00dc82 !important;
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    .feature-check {
        color: #1ab394;
        margin-right: 8px;
    }
    .card-description {
        color: #666;
        font-size: 0.95rem;
        min-height: 48px;
    }
    .feature-list {
        margin-top: 2rem;
    }
    .feature-list li {
        color: #444;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }
    .most-popular {
        background: #1ab394;
        color: white;
        text-align: center;
        padding: 8px;
        border-radius: 8px 8px 0 0;
        font-weight: 500;
    }
    .action-button {
        background: #1ab394 !important;
        border: none !important;
        padding: 12px 32px;
        font-weight: 500;
        width: 100%;
    }
    .action-button:hover {
        background: #149c81 !important;
    }
    .trial-text {
        color: #666;
        font-size: 0.9rem;
        margin-top: 1rem;
    }
    .card-body {
        padding: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.pricing-toggle button');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endpush