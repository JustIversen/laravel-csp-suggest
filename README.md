# Laravel Content Security Policy (CSP) wizard
Package that generates a configuration file for the [spatie/laravel-csp](https://github.com/spatie/laravel-csp) package.

Let the artisan wizard do the heavy lifting. Content Security Policy setup the easy way.

## What does the wizard do?
* Scans your http://APP_URL for resources.
* Asks you about every resource one-by-one.
* Asks you about some general CSP decisions.
* Suggests changes to your app resource-files.
* Outputs a configuration file you can use as a starting point for the [spatie/laravel-csp](https://github.com/spatie/laravel-csp) package.

This package aims to help you avoiding the console error debug-way of setting up your Content Security Policy.

## Installation
:dash: You can install the package via composer:

``composer require --dev justiversen/laravel-csp-suggest``

## Usage
Run the following artisan command to start the wizard:

``php artisan csp:suggest``

Answer the questions asked by the wizard, and it will create the policy for you.

The following arguments are available:

| Name | Description |
| --- | --- |
| --baseurl=[http://your-base-url] | Override the base URL. Default is APP_URL. |
| --raw | Ask the plugin to output the raw CSP policy together with the spatie configuration file generated. (TBD) |

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
