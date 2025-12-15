<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.driver' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    protected function authenticate(): array
    {
        $token = \Firebase\JWT\JWT::encode(
            ['nameid' => 1, 'exp' => time() + 3600],
            env('SECRET_KEY'),
            'HS256'
        );
        return ['Authorization' => "Bearer $token"];
    }
}
