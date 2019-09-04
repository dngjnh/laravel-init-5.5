<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Passport\Events\RefreshTokenCreated;
use App\Models\OauthRefreshToken;

class PruneOldTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RefreshTokenCreated  $event
     * @return void
     */
    public function handle(RefreshTokenCreated $event)
    {
        OauthRefreshToken::where('id', '<>', $event->refreshTokenId)
            ->where('access_token_id', '<>', $event->accessTokenId)
            ->update([
                'revoked' => true,
            ]);
    }
}
