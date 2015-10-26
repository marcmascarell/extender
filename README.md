# Extender

[![Latest Version](https://img.shields.io/github/release/marcmascarell/extender.svg?style=flat-square)](https://github.com/marcmascarell/extender/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Easily manage extensions or plugins

Installation
--------------
Require this package in your composer.json and run composer update:

    "mascame/extender": "dev-master"

or:

```sh
composer require mascame/extender
```

Usage
--------------

```php
$installer = new \Mascame\Extender\Installer\FileInstaller(
                 new \Mascame\Extender\Installer\FileWriter(),
                 'path-to-file'
             );

$manager = new \Mascame\Extender\Manager($installer);


class Foo() {}

// register plugin
$manager->add('foo-plugin', function() {
    return new Foo();
})

// boot the plugins
$manager->boot();

// install plugin
$manager->installer()->install('foo-plugin');

// uninstall plugin
$manager->installer()->uninstall('foo-plugin');
```
Support
----

If you want to give your opinion, you can send me an [email](mailto:marcmascarell@gmail.com), comment the project directly (if you want to contribute with information or resources) or fork the project and make a pull request.

Also I will be grateful if you want to make a donation, this project hasn't got a death date and it wants to be improved constantly:

[![Website Button](http://www.rahmenversand.com/images/paypal_logo_klein.gif "Donate!")](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=marcmascarell%40gmail%2ecom&lc=US&item_name=Artificer%20Development&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest&amount=5 "Contribute to the project")


License
----

MIT
