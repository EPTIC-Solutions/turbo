<?php

namespace Eptic\Turbo\Tests;

use Eptic\Turbo\TurboServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            TurboServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        /*
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_turbo_table.php.stub';
        $migration->up();
        */
    }
}
