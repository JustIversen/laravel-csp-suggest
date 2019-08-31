<?php

namespace JustIversen\CspSuggest\Tests;

use JustIversen\CspSuggest\CspSuggestServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            CspSuggestServiceProvider::class,
        ];
    }
}
