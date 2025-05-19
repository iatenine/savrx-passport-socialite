<?php

namespace Savrx\SavrxPassportSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class SavrxPassportExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('savrxpassport', Provider::class);
    }
}
