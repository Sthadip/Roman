<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['user_id','coin','side','coin_amount','usdt_amount','price','status','filled_by','filled_at','note'];
    protected $casts    = ['filled_at'=>'datetime','coin_amount'=>'decimal:8','usdt_amount'=>'decimal:8','price'=>'decimal:8'];

    public function isPending():  bool { return $this->status === 'pending'; }
    public function isFilled():   bool { return $this->status === 'filled'; }
    public function isCancelled():bool { return $this->status === 'cancelled'; }

    public function user()   { return $this->belongsTo(User::class); }
    public function filler() { return $this->belongsTo(User::class,'filled_by'); }
}
