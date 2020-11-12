@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.edit') }} {{ trans('cruds.code.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.codes.update", [$code->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label class="required" for="coupon_id">{{ trans('cruds.code.fields.coupon') }}</label>
                            <select class="form-control select2" name="coupon_id" id="coupon_id" required>
                                @foreach($coupons as $id => $coupon)
                                    <option value="{{ $id }}" {{ (old('coupon_id') ? old('coupon_id') : $code->coupon->id ?? '') == $id ? 'selected' : '' }}>{{ $coupon }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('coupon'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('coupon') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.coupon_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="code">{{ trans('cruds.code.fields.code') }}</label>
                            <input class="form-control" type="text" name="code" id="code" value="{{ old('code', $code->code) }}" required>
                            @if($errors->has('code'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('code') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.code_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="reserved_at">{{ trans('cruds.code.fields.reserved_at') }}</label>
                            <input class="form-control datetime" type="text" name="reserved_at" id="reserved_at" value="{{ old('reserved_at', $code->reserved_at) }}">
                            @if($errors->has('reserved_at'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('reserved_at') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.reserved_at_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="reserved_by_id">{{ trans('cruds.code.fields.reserved_by') }}</label>
                            <select class="form-control select2" name="reserved_by_id" id="reserved_by_id">
                                @foreach($reserved_bies as $id => $reserved_by)
                                    <option value="{{ $id }}" {{ (old('reserved_by_id') ? old('reserved_by_id') : $code->reserved_by->id ?? '') == $id ? 'selected' : '' }}>{{ $reserved_by }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('reserved_by'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('reserved_by') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.reserved_by_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="purchased_at">{{ trans('cruds.code.fields.purchased_at') }}</label>
                            <input class="form-control datetime" type="text" name="purchased_at" id="purchased_at" value="{{ old('purchased_at', $code->purchased_at) }}">
                            @if($errors->has('purchased_at'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('purchased_at') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.purchased_at_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="purchased_by_id">{{ trans('cruds.code.fields.purchased_by') }}</label>
                            <select class="form-control select2" name="purchased_by_id" id="purchased_by_id">
                                @foreach($purchased_bies as $id => $purchased_by)
                                    <option value="{{ $id }}" {{ (old('purchased_by_id') ? old('purchased_by_id') : $code->purchased_by->id ?? '') == $id ? 'selected' : '' }}>{{ $purchased_by }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('purchased_by'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('purchased_by') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.code.fields.purchased_by_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection