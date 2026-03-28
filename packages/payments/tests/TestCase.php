<?php

namespace Ecommerce\Payments\Tests;

use Ecommerce\Orders\OrdersServiceProvider;
use Ecommerce\Payments\PaymentsServiceProvider;
use Ecommerce\Products\ProductsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ProductsServiceProvider::class,
            OrdersServiceProvider::class,
            PaymentsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../products/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../../orders/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
