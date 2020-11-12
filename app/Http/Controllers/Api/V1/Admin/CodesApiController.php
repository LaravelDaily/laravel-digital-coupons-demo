<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use App\Http\Resources\Admin\CodeResource;
use App\Models\Code;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CodesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CodeResource(Code::with(['coupon', 'reserved_by', 'purchased_by'])->get());
    }

    public function store(StoreCodeRequest $request)
    {
        $code = Code::create($request->all());

        return (new CodeResource($code))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Code $code)
    {
        abort_if(Gate::denies('code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CodeResource($code->load(['coupon', 'reserved_by', 'purchased_by']));
    }

    public function update(UpdateCodeRequest $request, Code $code)
    {
        $code->update($request->all());

        return (new CodeResource($code))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Code $code)
    {
        abort_if(Gate::denies('code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $code->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
