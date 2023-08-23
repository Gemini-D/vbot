<?php

declare(strict_types=1);

namespace Hanson\Vbot\Support;

use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerInterface;

function app(): ContainerInterface
{
    return ApplicationContext::getContainer();
}
