<?php

namespace App\Http\Requests;

use App\Models\Code;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCodeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:codes,id',
        ];
    }
}
