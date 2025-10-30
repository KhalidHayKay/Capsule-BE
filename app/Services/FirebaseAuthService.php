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
        $envPath = env('FIREBASE_CREDENTIALS');

        // If path is relative, prepend base_path()
        $credentialsPath = str_starts_with($envPath, '/')
            ? $envPath
            : base_path($envPath);

        $this->auth = (new Factory)
            ->withServiceAccount($credentialsPath)
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

        $providerName = $verified->claims()->get('firebase')['sign_in_provider'];
        $providerId   = $verified->claims()->get('firebase')['identities'][$providerName][0];

        return [
            'fb_uid'         => $verified->claims()->get('sub'),
            'email'          => $verified->claims()->get('email'),
            'email_verified' => $verified->claims()->get('email_verified'),
            'name'           => $verified->claims()->get('name'),
            'avatar'         => $verified->claims()->get('picture'),

            // the social array must match the structure of the social_accounts table
            'social'         => [
                'provider_name' => $providerName,
                'provider_id'   => $providerId,
            ],
        ];
    }
}
