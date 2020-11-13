@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">{{ __('cruds.coupon.title') }}</div>

        <div class="card-body">
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="container mt-4">
                <div class="row justify-content-sm-center justify-content-md-start">
                    @foreach ($coupons as $coupon)
                        <div class="col-auto mb-4">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top" src="{{ $coupon->photo->preview ?? 'http://via.placeholder.com/286' }}" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $coupon->name }}</h5>
                                    <p class="card-text">Price: <b>${{ $coupon->price }}</b></p>
                                    <a href="{{ auth()->check() ? route('frontend.coupons.show', $coupon->id) : route('register') }}"
                                       class="btn btn-success">Purchase</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection