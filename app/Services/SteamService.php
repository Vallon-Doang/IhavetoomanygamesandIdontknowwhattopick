<?php

namespace App\Services;

use App\Exceptions\SteamApiException;
use Illuminate\Support\Facades\Http;

class SteamService
{
    private const OPENID_ENDPOINT = 'https://steamcommunity.com/openid/login';

    public function isConfigured(): bool
    {
        return filled(config('services.steam.key'));
    }

    /**
     * Build the Steam OpenID sign-in URL the member is redirected to.
     */
    public function getRedirectUrl(string $returnTo, ?string $realm = null): string
    {
        $parts = parse_url($returnTo);
        $realm ??= ($parts['scheme'] ?? 'http').'://'.($parts['host'] ?? 'localhost');

        return self::OPENID_ENDPOINT.'?'.http_build_query([
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $returnTo,
            'openid.realm' => $realm,
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ]);
    }

    /**
     * Verify a Steam OpenID callback and return the linked SteamID64, if valid.
     *
     * @param  array<string, mixed>  $query
     */
    public function verifyCallback(array $query): ?string
    {
        if (! isset($query['openid_claimed_id'], $query['openid_mode']) || $query['openid_mode'] !== 'id_res') {
            return null;
        }

        $params = [];
        foreach ($query as $key => $value) {
            if (str_starts_with($key, 'openid_')) {
                $params[str_replace('_', '.', $key)] = $value;
            }
        }
        $params['openid.mode'] = 'check_authentication';

        $response = Http::asForm()->timeout(10)->post(self::OPENID_ENDPOINT, $params);

        if (! $response->successful() || ! str_contains($response->body(), 'is_valid:true')) {
            return null;
        }

        if (preg_match('#/id/(\d+)$#', (string) $query['openid_claimed_id'], $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPlayerSummary(string $steamId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = Http::timeout(10)->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/', [
            'key' => config('services.steam.key'),
            'steamids' => $steamId,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('response.players.0');
    }

    /**
     * Fetch owned games. Returns null when the library is private/unavailable,
     * an empty array when the API confirms zero games.
     *
     * @return array<int, array<string, mixed>>|null
     *
     * @throws SteamApiException
     */
    public function getOwnedGames(string $steamId): ?array
    {
        if (! $this->isConfigured()) {
            throw new SteamApiException('Steam API key is not configured.');
        }

        $response = Http::timeout(20)->get('https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/', [
            'key' => config('services.steam.key'),
            'steamid' => $steamId,
            'include_appinfo' => 1,
            'include_played_free_games' => 1,
            'format' => 'json',
        ]);

        if (! $response->successful()) {
            throw new SteamApiException('Steam API request failed (HTTP '.$response->status().').');
        }

        $games = $response->json('response.games');

        if (! is_array($games)) {
            return null;
        }

        return $games;
    }
}