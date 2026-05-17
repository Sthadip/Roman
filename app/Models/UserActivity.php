<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = ['user_id','action','page','url','ip','last_seen_at'];
    protected $casts    = ['last_seen_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    // Upsert – one row per user, updated on each page hit
    public static function track(int $userId, string $action, string $page, string $url, ?string $ip): void
    {
        self::updateOrCreate(
            ['user_id' => $userId],
            ['action' => $action, 'page' => $page, 'url' => $url, 'ip' => $ip, 'last_seen_at' => now()]
        );
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }
}
