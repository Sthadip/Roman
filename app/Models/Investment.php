<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'user_id','package_id','amount','expected_return','profit',
        'coin','status','starts_at','ends_at','completed_at'
    ];
    protected $casts = [
        'amount'=>'decimal:8','expected_return'=>'decimal:8','profit'=>'decimal:8',
        'starts_at'=>'datetime','ends_at'=>'datetime','completed_at'=>'datetime'
    ];

    public function isActive(): bool    { return $this->status==='active'; }
    public function isCompleted(): bool { return $this->status==='completed'; }

    public function getDaysRemainingAttribute(): int {
        if($this->isCompleted()) return 0;
        return max(0,(int)now()->diffInDays($this->ends_at,false));
    }
    public function getProgressPercentAttribute(): int {
        $total = $this->starts_at->diffInSeconds($this->ends_at);
        if($total<=0) return 100;
        $elapsed = $this->starts_at->diffInSeconds(now());
        return min(100,(int)(($elapsed/$total)*100));
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function package() { return $this->belongsTo(InvestmentPackage::class,'package_id'); }
}
