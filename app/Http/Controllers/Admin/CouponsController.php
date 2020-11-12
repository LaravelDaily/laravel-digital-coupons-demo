<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCouponRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Code;
use App\Models\Coupon;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class CouponsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('coupon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $coupons = Coupon::withCount('codes', 'purchasedCodes')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        abort_if(Gate::denies('coupon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.coupons.create');
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

        if ($request->input('amount') > 0) {
            do {
                $codes = [];

                for ($i = 0; $i < $request->input('amount'); $i++) {
                    $codes[] = (string)mt_rand(pow(10, 10), pow(10, 11) - 1);
                }

                $codesUnique = Code::whereIn('code', $codes)->count() == 0;
            } while (!$codesUnique);

            foreach ($codes as $code) {
                $coupon->codes()->create([
                    'code' => $code
                ]);
            }
        }

        return redirect()->route('admin.coupons.index');
    }

    public function edit(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.coupons.edit', compact('coupon'));
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

        return redirect()->route('admin.coupons.index');
    }

    public function show(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.coupons.show', compact('coupon'));
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

    public function generateCodes(Request $request, Coupon $coupon) {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        if ($request->input('amount') > 0) {
            do {
                $codes = [];

                for ($i = 0; $i < $request->input('amount'); $i++) {
                    $codes[] = (string)mt_rand(pow(10, 10), pow(10, 11) - 1);
                }

                $codesUnique = Code::whereIn('code', $codes)->count() == 0;
            } while (!$codesUnique);

            foreach ($codes as $code) {
                $coupon->codes()->create([
                    'code' => $code
                ]);
            }
        }

        return redirect()->route('admin.coupons.index')->withMessage('Codes generated successfully');
    }
}
