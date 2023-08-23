<?php

declare(strict_types=1);

namespace Hanson\Vbot\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Migration\AbstractMigration;

class BaseMigration extends AbstractMigration
{
    /** @var \Illuminate\Database\Capsule\Manager */
    public $capsule;

    /** @var \Illuminate\Database\Schema\Builder */
    public $schema;

    public function init()
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'port' => DB_PORT,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}
