<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Code extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'codes';

    protected $dates = [
        'reserved_at',
        'purchased_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'coupon_id',
        'code',
        'reserved_at',
        'reserved_by_id',
        'purchased_at',
        'purchased_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function getReservedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setReservedAtAttribute($value)
    {
        $this->attributes['reserved_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function reserved_by()
    {
        return $this->belongsTo(User::class, 'reserved_by_id');
    }

    public function getPurchasedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setPurchasedAtAttribute($value)
    {
        $this->attributes['purchased_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function purchased_by()
    {
        return $this->belongsTo(User::class, 'purchased_by_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function scopeAvailableForUser($query)
    {
        return $query
            ->whereNull('purchased_at')
            ->where(function ($query) {
                $query->when(auth()->check(), function ($query) {
                    $query->where([
                            ['reserved_by_id', '=', auth()->id()],
                            ['reserved_at', '>', now()->subMinutes(10)]
                        ]);
                    })
                    ->orWhereNull('reserved_at');
            });
    }
}
