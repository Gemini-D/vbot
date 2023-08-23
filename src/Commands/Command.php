<?php

declare(strict_types=1);

namespace Hanson\Vbot\Commands;

use Symfony\Component\Console\Application;

/**
 * Class Command.
 */
class Command
{
    public function run()
    {
        $application = new Application();

        $application->run();
    }
}
