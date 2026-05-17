<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type','title','body','ref_user_id','is_read'];
    protected $casts    = ['is_read' => 'boolean'];

    public function refUser() { return $this->belongsTo(User::class, 'ref_user_id'); }

    public static function send(string $type, string $title, string $body, ?int $refUserId = null): void
    {
        self::create(['type' => $type, 'title' => $title, 'body' => $body, 'ref_user_id' => $refUserId]);
    }

    public static function unreadCount(): int
    {
        return self::where('is_read', false)->count();
    }
}
