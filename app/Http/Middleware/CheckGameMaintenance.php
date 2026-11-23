<?php

namespace App\Http\Middleware;

use App\Models\GameMaintenance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGameMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $gameKey): Response
    {
        if (GameMaintenance::isUnderMaintenance($gameKey)) {
            $message = GameMaintenance::getMaintenanceMessage($gameKey);
            $gameName = GameMaintenance::AVAILABLE_GAMES[$gameKey] ?? ucfirst($gameKey);

            if ($request->expectsJson() || $request->is('*/round', '*/themes', '*/scan', '*/vote', '*/state', '*/start*', '*/reveal', '*/next-round', '*/topics')) {
                return response()->json([
                    'status' => 'maintenance',
                    'message' => $message,
                    'game' => $gameName,
                ], 503);
            }

            return response()->view('errors.game-maintenance', [
                'gameKey' => $gameKey,
                'gameName' => $gameName,
                'message' => $message,
            ], 503);
        }

        return $next($request);
    }
}
