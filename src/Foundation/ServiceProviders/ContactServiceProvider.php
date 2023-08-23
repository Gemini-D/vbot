<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Contact\Contacts;
use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;
use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Contact\Officials;
use Hanson\Vbot\Contact\Specials;
use Hanson\Vbot\Core\ContactFactory;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ContactServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->bind('contactFactory', function () use ($pimple) {
            return new ContactFactory($pimple);
        });
        $pimple->singleton('myself', function () {
            return new Myself();
        });
        $pimple->singleton('friends', function () use ($pimple) {
            return (new Friends())->setVbot($pimple);
        });
        $pimple->singleton('groups', function () use ($pimple) {
            return (new Groups())->setVbot($pimple);
        });
        $pimple->singleton('members', function () use ($pimple) {
            return (new Members())->setVbot($pimple);
        });
        $pimple->singleton('officials', function () use ($pimple) {
            return (new Officials())->setVbot($pimple);
        });
        $pimple->singleton('specials', function () use ($pimple) {
            return (new Specials())->setVbot($pimple);
        });
        $pimple->singleton('contacts', function () use ($pimple) {
            return (new Contacts())->setVbot($pimple);
        });
    }
}
