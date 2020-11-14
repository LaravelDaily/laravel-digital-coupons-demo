@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.show') }} {{ trans('cruds.coupon.title') }}
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.coupons.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.coupon.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $coupon->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.coupon.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $coupon->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.coupon.fields.price') }}
                                    </th>
                                    <td>
                                        {{ $coupon->price }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.coupon.fields.photo') }}
                                    </th>
                                    <td>
                                        @if($coupon->photo)
                                            <a href="{{ $coupon->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $coupon->photo->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.coupons.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Purchase Now</div>
                <div class="card-body">
                    <p>
                        Coupon code reserved. Time to buy:
                        <span id="timer">{{ (int)($timerSeconds / 60) }}:{{ ($timerSeconds % 60) < 10 ? '0' : '' }}{{ $timerSeconds % 60 }}</span>
                    </p>
                    <p>The price of a coupon is ${{ $coupon->price }}.</p>
                    <form method="POST" action="{{ route('frontend.codes.purchase', $code) }}" class="card-form mt-3 mb-3">
                        @csrf
                        <input type="hidden" name="payment_method" class="payment-method">
                        <input class="StripeElement mb-3" name="card_holder_name" placeholder="Card holder name" required>
                        <div class="col-lg-4 col-md-6">
                            <div id="card-element"></div>
                        </div>
                        <div id="card-errors" role="alert"></div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary pay">
                                Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    let secondsLeft = {{ $timerSeconds }}, formattedTime;

    let timerInterval = setInterval(function () {
        secondsLeft--;
        let minutes = secondsLeft / 60, seconds = secondsLeft % 60;
        formattedTime = Math.floor(minutes) + ":" + (seconds < 10 ? '0' : '') + seconds;
        $('span#timer').text(formattedTime);

        if (secondsLeft <= 0) {
            alert("Sorry, you didn't purchase on time");
            window.location.href = "{{ route('frontend.coupons.index') }}";
        }
    }, 1000);

    let stripe = Stripe("{{ env('STRIPE_KEY') }}")
    let elements = stripe.elements()
    let style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    }
    let card = elements.create('card', {style: style})
    card.mount('#card-element')
    let paymentMethod = null
    $('.card-form').on('submit', function (e) {
        $('button.pay').attr('disabled', true)
        if (paymentMethod) {
            return true
        }
        stripe.confirmCardSetup(
            "{{ $intent->client_secret }}",
            {
                payment_method: {
                    card: card,
                    billing_details: {name: $('.card_holder_name').val()}
                }
            }
        ).then(function (result) {
            if (result.error) {
                $('#card-errors').text(result.error.message)
                $('button.pay').removeAttr('disabled')
            } else {
                paymentMethod = result.setupIntent.payment_method
                $('.payment-method').val(paymentMethod)
                $('.card-form').submit()
            }
        })
        return false
    })
</script>
@endsection