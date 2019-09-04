<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Passport\Events\AccessTokenCreated;
use App\Models\OauthAccessToken;

class RevokeOldTokens
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
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        OauthAccessToken::where('id', '<>', $event->tokenId)
            ->where('user_id', '=', $event->userId)
            ->where('client_id', '=', $event->clientId)
            ->update([
                'revoked' => true,
            ]);
    }
}
