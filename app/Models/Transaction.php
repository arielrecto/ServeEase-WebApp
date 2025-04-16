<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'payment_account_id',
        'transaction_type',
        'amount',
        'currency',
        'transactionable_id',
        'transactionable_type',
        'paid_by',
        'paid_to',
        'status',
        'reference_number',
    ];


    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class);
    }
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
    public function paidTo()
    {
        return $this->belongsTo(User::class, 'paid_to');
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
