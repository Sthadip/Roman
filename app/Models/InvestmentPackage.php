<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvestmentPackage extends Model
{
    protected $fillable = ['name','description','duration_days','min_amount','max_amount','return_rate','is_active'];
    protected $casts    = ['is_active'=>'boolean','min_amount'=>'decimal:8','max_amount'=>'decimal:8','return_rate'=>'decimal:4'];

    public function investments() { return $this->hasMany(Investment::class,'package_id'); }

    public function getReturnLabelAttribute(): string {
        return number_format($this->return_rate,2).'% in '.$this->duration_days.' day'.($this->duration_days>1?'s':'');
    }
    public function calcProfit(float $amount): float {
        return round($amount * ($this->return_rate / 100), 8);
    }
    public function calcReturn(float $amount): float {
        return round($amount + $this->calcProfit($amount), 8);
    }
}
