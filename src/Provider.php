<?php

namespace Savrx\SavrxPassportSocialite;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'SAVRXPASSPORT';

    protected $scopeSeparator = ' ';

    public static function additionalConfigKeys(): array
    {
        return [
            'host',
            'authorize_url',
            'token_url',
            'register_url',
            'verify_url',
            'revoke_url',
            'profile_url',
            'logout_redirect',
            'userinfo_url',
        ];
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->getSavrxPassportUrl('authorize_url'), $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->getSavrxPassportUrl('token_url');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this
            ->getHttpClient()
            ->get(
                $this->getSavrxPassportUrl('userinfo_url'),
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]
            );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        if (empty($user['data'])) {
            $user['data'] = [];
        }

        if (empty($user['data']['user'])) {
            $user['data']['user'] = [];
        }
        return (new User)->setRaw($user)->map([
            'id'       => $user['data']['user']['passport_id'] ?? null,
            'nickname' => $user['data']['user']['display_name'] ?? null,
            'name'     => $user['data']['user']['display_name'] ?? null,
            'email'    => $user['data']['user']['email'] ?? null,
            'avatar'   => null,
        ]);
    }


    protected function getSavrxPassportUrl($type)
    {
        return rtrim($this->getConfig('host'), '/') . '/' . ltrim($this->getConfig($type, Arr::get([
            'authorize_url' => 'oauth/authorize',
            'token_url'     => 'oauth/token',
            'register_url' => 'register',
            'verify_url' => 'api/v1/verify',
            'revoke_url' => 'oauth/token',
            'profile_url' => 'profile',
            'logout_redirect' => 'logout',
            'userinfo_url' => 'api/v1/user',
        ], $type)), '/');
    }
}
