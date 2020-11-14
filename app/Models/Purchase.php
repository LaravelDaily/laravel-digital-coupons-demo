<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Purchase extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'purchases';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'code_id',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function code()
    {
        return $this->belongsTo(Code::class, 'code_id');
    }

    public function scopeAvailableForUser($query)
    {
        return $query->when(auth()->check(), function ($query) {
            $query->where('user_id', auth()->id());
        });
    }
}
