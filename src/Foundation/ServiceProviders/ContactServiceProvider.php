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
use Pimple\Container;

class ContactServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['contactFactory'] = function () use ($pimple) {
            return new ContactFactory($pimple);
        };
        $pimple['myself'] = function () use ($pimple) {
            return (new Myself())->setVbot($pimple);
        };
        $pimple['friends'] = function () use ($pimple) {
            return (new Friends())->setVbot($pimple);
        };
        $pimple['groups'] = function () use ($pimple) {
            return (new Groups())->setVbot($pimple);
        };
        $pimple['members'] = function () use ($pimple) {
            return (new Members())->setVbot($pimple);
        };
        $pimple['officials'] = function () use ($pimple) {
            return (new Officials())->setVbot($pimple);
        };
        $pimple['specials'] = function () use ($pimple) {
            return (new Specials())->setVbot($pimple);
        };
        $pimple['contacts'] = function () use ($pimple) {
            return (new Contacts())->setVbot($pimple);
        };
    }
}
