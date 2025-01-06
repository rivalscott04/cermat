<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'amount_paid',
        'payment_status',
        'payment_method',
        'transaction_id'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}