<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    const ROLE_USER        = 'user';
    const ROLE_ADMIN       = 'admin';
    const ROLE_SUPER_ADMIN = 'super_admin';

    protected $fillable = ['name','email','password','role','google_id','avatar','email_verified_at'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','password'=>'hashed'];

    public function isAdmin(): bool      { return in_array($this->role,[self::ROLE_ADMIN,self::ROLE_SUPER_ADMIN]); }
    public function isSuperAdmin(): bool { return $this->role === self::ROLE_SUPER_ADMIN; }
    public function isRegularAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
    public function isUser(): bool       { return $this->role === self::ROLE_USER; }
    public function isGoogleUser(): bool { return !is_null($this->google_id); }
    public function getRoleLabelAttribute(): string {
        return match($this->role) { 'super_admin'=>'Super Admin', 'admin'=>'Admin', default=>'User' };
    }

    public function wallets()      { return $this->hasMany(Wallet::class); }
    public function deposits()     { return $this->hasMany(Deposit::class); }
    public function withdrawals()  { return $this->hasMany(Withdrawal::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function investments()  { return $this->hasMany(Investment::class); }
    public function kyc()          { return $this->hasOne(KycVerification::class)->latestOfMany(); }

    public function hasApprovedKyc(): bool {
        return KycVerification::where('user_id',$this->id)->where('status','approved')->exists();
    }
    public function hasSubmittedKyc(): bool {
        return KycVerification::where('user_id',$this->id)->whereIn('status',['pending','approved'])->exists();
    }
}
