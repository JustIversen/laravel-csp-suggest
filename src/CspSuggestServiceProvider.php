<?php

namespace JustIversen\CspSuggest;

use JustIversen\CspSuggest\CspSuggestCommand;
use Illuminate\Support\ServiceProvider;

class CspSuggestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerArtisanCommands();
        }
    }
    
    public function register(): void
    {
        //
    }

    /**
     * Register the artisan commands.
     */
    protected function registerArtisanCommands(): void
    {
        $this->commands([
            CspSuggestCommand::class,
        ]);
    }
}
