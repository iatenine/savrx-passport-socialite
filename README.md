# Sav-Rx Passport Socialite Provider

```bash
composer require savrx-cloud/savrx-passport-socialite
```

## Installation & Basic Usage

Please see the [Base Installation Guide](https://socialiteproviders.com/usage/), then follow the provider specific instructions below.

### Add configuration to `config/services.php`

```php
'savrxpassport' => [    
  'client_id' => env('SAVRXPASSPORT_CLIENT_ID'),  
  'client_secret' => env('SAVRXPASSPORT_CLIENT_SECRET'),  
  'redirect' => env('SAVRXPASSPORT_REDIRECT_URI'),
  'host' => env('SAVRXPASSPORT_HOST'),
],
```

`host` will generally always be set to `https://auth.savrx.com/` but may be altered to accomodate separate dev environments
`redirect` will be used to determine the redirect URI you wish to use

### Add provider event listener

#### Laravel 11+

In Laravel 11, the default `EventServiceProvider` provider was removed. Instead, add the listener using the `listen` method on the `Event` facade, in your `AppServiceProvider` `boot` method.

* Note: You do not need to add anything for the built-in socialite providers unless you override them with your own providers.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('savrxpassport', \Savrx\SavrxPassportSocialite\Provider::class);
        });
    }
}

```
<details>
<summary>
Laravel 10 or below
</summary>
Configure the package's listener to listen for `SocialiteWasCalled` events.

Add the event to your `listen[]` array in `app/Providers/EventServiceProvider`. See the [Base Installation Guide](https://socialiteproviders.com/usage/) for detailed instructions.

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // ... other providers
        Savrx\SavrxPassportSocialite\SavrxPassportExtendSocialite::class.'@handle',
    ],
];
```
</details>

### Usage

You should now be able to use the provider like you would regularly use Socialite (assuming you have the facade installed):

```php
// Initial redirect
public function redirect() {
    // Remember to set scopes accordingly
    return Socialite::driver('savrxpassport')
        ->setScopes([
            'read-user',
            'verify-admin-portal-access'
        ])
        ->redirect();
}

//
public function callback() {
    $user = Socialite::driver('savrxpassport')->user();
    if (!$user || !$user->token || $user->refreshToken) {
        // handle auth failures
        return redirect('login.index');
    }
    // authenticate
    return redirect('dashboard.index');
    }
```

### Returned User fields

- ``id``
- ``nickname``
- ``name``
- ``email``
- ``avatar``

* name and nickname are equivalent
* avatar is always `null`
