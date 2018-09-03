# laravel-csp-suggest
Package that generates a configuration file for the [spatie/laravel-csp](https://github.com/spatie/laravel-csp) package.

Let the artisan wizard do the heavy lifting. Content Security Policy setup the easy way.

The wizard will:
* Scan your http://APP_URL for resources.
* Ask you about every resource one-by-one.
* Ask you about some general CSP decisions.
* Suggest changes to your app resource-files.
* Output a configuration file you can use as a starting point for the [spatie/laravel-csp](https://github.com/spatie/laravel-csp) package.

This package aims to help you avoiding the console error debug-way of setting up your Content Security Policy.

## Installation
You can install the package via composer:

``composer require --dev KristianI/laravel-csp-suggest``

## Usage
Run the following artisan command to start the wizard:

``php artisan cspsuggest``

The wizard will create the policy for you, if only you answer the questions asked.

The following arguments are available:

| Name | Description |
| --- | --- |
| -y | Don't ask about whether to include the resource or not, just include all. |
| --url=.. | Override the URL to scan. Default is APP_URL. |
| --raw | Ask the plugin to output the raw CSP policy together with the spatie configuration file generated. |

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
