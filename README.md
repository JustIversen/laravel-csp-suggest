# laravel-csp-suggest
Package that generates a configuration file for the [spatie/laravel-csp package](https://github.com/spatie/laravel-csp).

The package contains an artisan command that acts as a wizard. The wizard
1. Scans your APP_URL for resources
2. Groups the resources
3. Asks you about every resource one-by-one
4. Asks you about some general CSP decisions
5. Outputs configuration file, you can use as a starting point for the spatie/laravel-csp package.

This package aims to help you avoiding the console error debug-way of setting up your CSP policy.

## Installation
You can install the package via composer:

``composer require --dev KristianI/laravel-csp-suggest``

## Usage
Run the following artisan command to start the wizard:

``php artisan cspsuggest``

The following arguments are available:

| Name | Description |
| --- | --- |
| -y | Don't ask about whether to include the resource or not, just include all. |
| --url=.. | Override the URL to scan. Default is APP_URL. |
| --raw | Ask the plugin to output the raw CSP policy together with the spatie configuration file generated. |

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
