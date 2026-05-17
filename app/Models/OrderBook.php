<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderBook extends Model
{
    protected $table    = 'order_book';
    protected $fillable = ['user_id','coin','side','trigger_price','coin_amount','usdt_amount','status','filled_at','trade_id'];
    protected $casts    = ['filled_at'=>'datetime','trigger_price'=>'decimal:8','coin_amount'=>'decimal:8','usdt_amount'=>'decimal:8'];

    public function isOpen():     bool { return $this->status === 'open'; }
    public function isFilled():   bool { return $this->status === 'filled'; }
    public function isCancelled():bool { return $this->status === 'cancelled'; }

    public function user()  { return $this->belongsTo(User::class); }
    public function trade() { return $this->belongsTo(Trade::class); }
}
