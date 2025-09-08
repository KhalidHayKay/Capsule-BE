<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseAuthService
{
    protected FirebaseAuth $auth;

    public function __construct()
    {
        $this->auth = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createAuth();
    }

    public function verifyIdToken(string $idToken)
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (FailedToVerifyToken $e) {
            return null;
        }
    }

    public function getUserData($idToken)
    {
        $verified = $this->verifyIdToken($idToken);

        if (! $verified)
            return null;

        return [
            'uid'           => $verified->claims()->get('sub'),
            'email'         => $verified->claims()->get('email'),
            'name'          => $verified->claims()->get('name'),
            'avatar'        => $verified->claims()->get('picture'),

            'provider_name' => $verified->claims()->get('firebase')['sign_in_provider'] ?? 'firebase',
            // 'provider_id' => $verified->claims()->get('firebase')['sign_in_provider'] ?? 'firebase',
        ];
    }
}
