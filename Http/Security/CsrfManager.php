<?php

namespace TelNowEdge\FreePBX\Base\Http\Security;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

class CsrfManager
{
    public static function create()
    {
        /**
         * migrate prevent that the session initialize by FPX conflict with Symfony components
         */
        $session = new Session();
        $session->migrate();

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($session);

        return new CsrfTokenManager($csrfGenerator, $csrfStorage);
    }
}
