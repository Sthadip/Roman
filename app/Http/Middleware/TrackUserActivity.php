<?php
namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    // Map route names → human readable labels
    private const PAGE_LABELS = [
        'user.dashboard'        => 'Dashboard',
        'user.wallet'           => 'Wallet',
        'user.deposit.form'     => 'Deposit Page',
        'user.deposit.history'  => 'Deposit History',
        'user.withdraw.form'    => 'Withdrawal Page',
        'user.withdraw.history' => 'Withdrawal History',
        'user.transactions'     => 'Transactions',
        'user.kyc'              => 'KYC Verification',
        'user.trade'            => 'Trading',
        'user.profile'          => 'Profile Settings',
        'user.invest'           => 'Investment Plans',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && auth()->user()->isUser()) {
            $routeName = $request->route()?->getName() ?? 'unknown';
            $page      = self::PAGE_LABELS[$routeName] ?? ucwords(str_replace(['.','_','-'], ' ', $routeName));

            UserActivity::track(
                auth()->id(),
                $routeName,
                $page,
                $request->path(),
                $request->ip()
            );
        }

        return $response;
    }
}
