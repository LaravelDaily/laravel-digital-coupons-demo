<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCouponRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class CouponsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        $coupons = Coupon::whereHas('codes', function ($query) {
                $query->availableForUser();
            })
            ->get();

        return view('frontend.coupons.index', compact('coupons'));
    }

    public function create()
    {
        abort_if(Gate::denies('coupon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.coupons.create');
    }

    public function store(StoreCouponRequest $request)
    {
        $coupon = Coupon::create($request->all());

        if ($request->input('photo', false)) {
            $coupon->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $coupon->id]);
        }

        return redirect()->route('frontend.coupons.index');
    }

    public function edit(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('frontend.coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->all());

        if ($request->input('photo', false)) {
            if (!$coupon->photo || $request->input('photo') !== $coupon->photo->file_name) {
                if ($coupon->photo) {
                    $coupon->photo->delete();
                }

                $coupon->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($coupon->photo) {
            $coupon->photo->delete();
        }

        return redirect()->route('frontend.coupons.index');
    }

    public function show(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $code = $coupon->reserved_code;

        if (!$code) {
            return redirect()
                ->route('frontend.coupons.index')
                ->withErrors(["Sorry, this coupon has no codes available"]);
        }

        $intent       = auth()->user()->createSetupIntent();
        $timerSeconds = now()->diffInSeconds(Carbon::parse($code->reserved_at)->addMinutes(10), false);

        return view('frontend.coupons.show', compact('coupon', 'intent', 'code', 'timerSeconds'));
    }

    public function destroy(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupon->delete();

        return back();
    }

    public function massDestroy(MassDestroyCouponRequest $request)
    {
        Coupon::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('coupon_create') && Gate::denies('coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Coupon();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
