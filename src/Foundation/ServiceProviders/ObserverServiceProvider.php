<?php

declare(strict_types=1);

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Observers\BeforeMessageObserver;
use Hanson\Vbot\Observers\ExitObserver;
use Hanson\Vbot\Observers\FetchContactObserver;
use Hanson\Vbot\Observers\LoginSuccessObserver;
use Hanson\Vbot\Observers\NeedActivateObserver;
use Hanson\Vbot\Observers\Observer;
use Hanson\Vbot\Observers\QrCodeObserver;
use Hanson\Vbot\Observers\ReLoginSuccessObserver;
use Pimple\Container;

class ObserverServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['observer'] = function () use ($pimple) {
            return new Observer($pimple);
        };
        $pimple['qrCodeObserver'] = function () use ($pimple) {
            return new QrCodeObserver($pimple);
        };
        $pimple['loginSuccessObserver'] = function () use ($pimple) {
            return new LoginSuccessObserver($pimple);
        };
        $pimple['reLoginSuccessObserver'] = function () use ($pimple) {
            return new ReLoginSuccessObserver($pimple);
        };
        $pimple['exitObserver'] = function () use ($pimple) {
            return new ExitObserver($pimple);
        };
        $pimple['fetchContactObserver'] = function () use ($pimple) {
            return new FetchContactObserver($pimple);
        };
        $pimple['beforeMessageObserver'] = function () use ($pimple) {
            return new BeforeMessageObserver($pimple);
        };
        $pimple['needActivateObserver'] = function () use ($pimple) {
            return new NeedActivateObserver($pimple);
        };
    }
}
