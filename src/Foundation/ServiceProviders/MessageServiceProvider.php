<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\MessageFactory;
use Hanson\Vbot\Core\MessageHandler;
use Hanson\Vbot\Core\ShareFactory;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Text;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $pimple)
    {
        $pimple->singleton('messageHandler', function () use ($pimple) {
            return new MessageHandler($pimple);
        });
        $pimple->singleton('messageFactory', function () use ($pimple) {
            return new MessageFactory($pimple);
        });
        $pimple->singleton('shareFactory', function () use ($pimple) {
            return new ShareFactory($pimple);
        });

        //        $vbot->bind('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
    }
}
