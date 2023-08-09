<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
    ];

    public static function setOtpForUser(int $user_id, int $otp)
    {
        self::where('user_id', $user_id)->delete();

        return self::create([
            'user_id' => $user_id,
            'otp' => $otp
        ]);
    }

    public static function validateOtp(int $user_id, int $otp, int $lastSecond = 60): bool
    {
        $otp = self::query()
            ->where('user_id', $user_id)
            ->where('otp', $otp)
            ->where('created_at', '>=', Carbon::now()->subSeconds($lastSecond))
            ->exists();
//        self::where('user_id', $user_id)->delete();
        return $otp;
    }

}
