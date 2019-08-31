<?php

namespace JustIversen\CspSuggest\BrowserDrivers;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Str;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverPoint;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

/**
 * Browser implementation of Facebook Webdriver.
 * 
 * @source https://github.com/laravel/dusk/
 */
class FacebookWebdriver
{
    /**
     * The base URL for all URLs.
     *
     * @var string
     */
    public static $baseUrl;

    /**
     * The directory that will contain any console logs.
     *
     * @var string
     */
    public static $storeConsoleLogAt;

    /**
     * The browsers that support retrieving logs.
     *
     * @var array
     */
    public static $supportsRemoteLogs = [
        WebDriverBrowserType::CHROME,
        WebDriverBrowserType::PHANTOMJS,
    ];

    /**
     * Get the callback which resolves the default user to authenticate.
     *
     * @var \Closure
     */
    public static $userResolver;

    /**
     * The default wait time in seconds.
     *
     * @var int
     */
    public static $waitSeconds = 5;

    /**
     * The RemoteWebDriver instance.
     *
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public $driver;

    /**
     * The element resolver instance.
     *
     * @var ElementResolver
     */
    public $resolver;

    /**
     * The page object currently being viewed.
     *
     * @var mixed
     */
    public $page;

    /**
     * The component object currently being viewed.
     *
     * @var mixed
     */
    public $component;

    /**
     * Create a browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @param  ElementResolver  $resolver
     * @return void
     */
    public function __construct($driver, $resolver = null)
    {
        $this->driver = $driver;

        $this->resolver = $resolver ?: new ElementResolver($driver);
    }

    /**
     * Browse to the given URL.
     *
     * @param  string|Page  $url
     * @return $this
     */
    public function visit($url)
    {
        // First, if the URL is an object it means we are actually dealing with a page
        // and we need to create this page then get the URL from the page object as
        // it contains the URL. Once that is done, we will be ready to format it.
        if (is_object($url)) {
            $page = $url;

            $url = $page->url();
        }

        // If the URL does not start with http or https, then we will prepend the base
        // URL onto the URL and navigate to the URL. This will actually navigate to
        // the URL in the browser. Then we will be ready to make assertions, etc.
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            $url = static::$baseUrl.'/'.ltrim($url, '/');
        }

        $this->driver->navigate()->to($url);

        // If the page variable was set, we will call the "on" method which will set a
        // page instance variable and call an assert method on the page so that the
        // page can have the chance to verify that we are within the right pages.
        if (isset($page)) {
            $this->on($page);
        }

        return $this;
    }

    /**
     * Browse to the given route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @return $this
     */
    public function visitRoute($route, $parameters = [])
    {
        return $this->visit(route($route, $parameters));
    }

    /**
     * Set the current page object.
     *
     * @param  mixed  $page
     * @return $this
     */
    public function on($page)
    {
        $this->onWithoutAssert($page);

        $page->assert($this);

        return $this;
    }

    /**
     * Set the current page object without executing the assertions.
     *
     * @param  mixed  $page
     * @return $this
     */
    public function onWithoutAssert($page)
    {
        $this->page = $page;

        // Here we will set the page elements on the resolver instance, which will allow
        // the developer to access short-cuts for CSS selectors on the page which can
        // allow for more expressive navigation and interaction with all the pages.
        $this->resolver->pageElements(array_merge(
            $page::siteElements(), $page->elements()
        ));

        return $this;
    }

    /**
     * Refresh the page.
     *
     * @return $this
     */
    public function refresh()
    {
        $this->driver->navigate()->refresh();

        return $this;
    }

    /**
     * Navigate to the previous page.
     *
     * @return $this
     */
    public function back()
    {
        $this->driver->navigate()->back();

        return $this;
    }

    /**
     * Maximize the browser window.
     *
     * @return $this
     */
    public function maximize()
    {
        $this->driver->manage()->window()->maximize();

        return $this;
    }

    /**
     * Store the console output with the given name.
     *
     * @param  string  $name
     * @return $this
     */
    public function storeConsoleLog($name)
    {
        if (in_array($this->driver->getCapabilities()->getBrowserName(), static::$supportsRemoteLogs)) {
            $console = $this->driver->manage()->getLog('browser');

            if (! empty($console)) {
                file_put_contents(
                    sprintf('%s/%s.log', rtrim(static::$storeConsoleLogAt, '/'), $name), json_encode($console, JSON_PRETTY_PRINT)
                );
            }
        }

        return $this;
    }

    /**
     * Execute a Closure with a scoped browser instance.
     *
     * @param  string  $selector
     * @param  \Closure  $callback
     * @return $this
     */
    public function within($selector, Closure $callback)
    {
        return $this->with($selector, $callback);
    }

    /**
     * Execute a Closure with a scoped browser instance.
     *
     * @param  string  $selector
     * @param  \Closure  $callback
     * @return $this
     */
    public function with($selector, Closure $callback)
    {
        $browser = new static(
            $this->driver, new ElementResolver($this->driver, $this->resolver->format($selector))
        );

        if ($this->page) {
            $browser->onWithoutAssert($this->page);
        }

        if ($selector instanceof Component) {
            $browser->onComponent($selector, $this->resolver);
        }

        call_user_func($callback, $browser);

        return $this;
    }

    /**
     * Set the current component state.
     *
     * @param  \Laravel\Dusk\Component  $component
     * @param  \Laravel\Dusk\ElementResolver  $parentResolver
     * @return void
     */
    public function onComponent($component, $parentResolver)
    {
        $this->component = $component;

        // Here we will set the component elements on the resolver instance, which will allow
        // the developer to access short-cuts for CSS selectors on the component which can
        // allow for more expressive navigation and interaction with all the components.
        $this->resolver->pageElements(
            $component->elements() + $parentResolver->elements
        );

        $component->assert($this);

        $this->resolver->prefix = $this->resolver->format(
            $component->selector()
        );
    }

    /**
     * Pause for the given amount of milliseconds.
     *
     * @param  int  $milliseconds
     * @return $this
     */
    public function pause($milliseconds)
    {
        usleep($milliseconds * 1000);

        return $this;
    }

    /**
     * Close the browser.
     *
     * @return void
     */
    public function quit()
    {
        $this->driver->quit();
    }

    /**
     * Tap the browser into a callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function tap($callback)
    {
        $callback($this);

        return $this;
    }

    /**
     * Dump the content from the last response.
     *
     * @return void
     */
    public function dump()
    {
        dd($this->driver->getPageSource());
    }

    /**
     * Stop running tests but leave the browser open.
     *
     * @return void
     */
    public function stop()
    {
        exit();
    }

    /**
     * Dynamically call a method on the browser.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($this->component && method_exists($this->component, $method)) {
            array_unshift($parameters, $this);

            $this->component->{$method}(...$parameters);

            return $this;
        }

        if ($this->page && method_exists($this->page, $method)) {
            array_unshift($parameters, $this);

            $this->page->{$method}(...$parameters);

            return $this;
        }

        throw new BadMethodCallException("Call to undefined method [{$method}].");
    }
}