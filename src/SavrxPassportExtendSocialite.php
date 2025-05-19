<?php

namespace SocialiteProviders\SavrxPassport;

use SocialiteProviders\Manager\SocialiteWasCalled;

class SavrxPassportExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('savrxpassport', Provider::class);
    }
}
