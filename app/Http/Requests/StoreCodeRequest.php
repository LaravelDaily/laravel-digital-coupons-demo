<?php

namespace App\Http\Requests;

use App\Models\Code;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCodeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('code_create');
    }

    public function rules()
    {
        return [
            'coupon_id'    => [
                'required',
                'integer',
            ],
            'code'         => [
                'string',
                'required',
            ],
            'reserved_at'  => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'purchased_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}
