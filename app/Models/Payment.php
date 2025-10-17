<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'payment_number',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'payment_proof_path',
        'status',
        'verification_notes',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Accessors & Mutators
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date->format('d/m/Y');
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
            'online' => 'Pembayaran Online',
            'mobile_banking' => 'Mobile Banking',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Helper methods
     */
    public function canBeVerified()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function hasProof()
    {
        return !empty($this->payment_proof_path);
    }
}
