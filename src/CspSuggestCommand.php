<?php

namespace JustIversen\CspSuggest;

use Illuminate\Console\Command;
use JustIversen\CspSuggest\Routes;
use JustIversen\CspSuggest\Crawler;
use JustIversen\CspSuggest\ContentUrl;
use JustIversen\CspSuggest\ConfigurationFile;

class CspSuggestCommand extends Command
{
    protected $signature = 'csp:suggest';

    protected $description = 'Command that generates a configuration file for the spatie/laravel-csp package.';

    /**
     * Routes.
     *
     * @var Routes
     */
    protected $routes;

    /**
     * Content URL.
     *
     * @var ContentUrl
     */
    protected $contentUrl;

    /**
     * Configuration File.
     *
     * @var ConfigurationFile
     */
    protected $configurationFile;

    /**
     * Construct class with dependencies.
     *
     * @param  Routes $routes
     * @param  ContentUrl $contentUrl
     * @param  ConfigurationFile $configurationFile
     */
    public function __construct(
        Routes $routes,
        ContentUrl $contentUrl,
        ConfigurationFile $configurationFile
    ) {
        $this->routes = $routes;
        $this->contentUrl = $contentUrl;
        $this->configurationFile = $configurationFile;
    }

    /**
     * Command execution.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Now scanning web endpoints for URL\'s to include in the Content Security Policy.');

        $urls = $this->routes->get()->map(function ($route) {
            return $this->contentUrl->extract((new Crawler)($route));
        })->flatten();

        $filename = $this->configurationFile->add($urls)->build()->persistToFile();

        $this->info(sprintf('Content Security Policy configuration built and saved in "%s"!', $filename));
    }
}
