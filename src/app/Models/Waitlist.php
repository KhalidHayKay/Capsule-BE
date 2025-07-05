<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'referral_code',
        'referred_by',
    ];

    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::creating(function (self $waitlist) {
            if (empty($waitlist->referral_code)) {
                $waitlist->referral_code = self::generateReferralCode();
            }
        });
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = 'REF_' . random_int(100000, 999999);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
}
