<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\Assert as PHPUnit;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // phpunit will stop because run out of memory so we need increase memory limit before run test
        ini_set('memory_limit', '1024M');
    }

    /**
     * Get the base API URL path
     *
     * @param string $endpoint
     */
    protected function getBaseAPIUrlPath()
    {
        return '/api';
    }

    /**
     * Get the full URL path
     *
     * @param string $endpoint
     */
    protected function getUrlPath($endpoint = null)
    {
        if ($endpoint !== null && substr($endpoint, 0, 1) !== '/') {
            $endpoint = '/' . $endpoint;
        }

        return sprintf('/api/%s%s', $this->urlBasePath, $endpoint);
    }
}
