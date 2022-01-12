# Rector Drupal Configurator

Provides utility for configuring [Rector](https://getrector.org) to understand a Drupal 8+ codebase.

### Usage

```
$ composer require mxr576/rector-drupal-configurator
$ ./vendor/bin/rector init
```

and call

```php
    $drc = new \mxr576\RectorDrupalConfigurator\RectorDrupalConfigurator();
    $drc->configure($containerConfigurator);
```

at the beginning of the generated function in `rector.php`. See [example.rector.php](example.rector.php).

### What is a difference between this library and `palantirnet/drupal-rector`?

[palantirnet/drupal-rector](https://github.com/palantirnet/drupal-rector) not just configures Rector to understand a
Drupal 8+ codebase but also includes (only) Drupal specific Rector rules in the `rector.php` that it provides.
This library decouples the "Drupal configuration" part and (only) provides a standalone solution that configures Rector
to understand a Drupal 8+ codebase in _any `rector.php`_. What other Rector configurations (e.g.: rules) you enable in
that rector.php it is up to you. See [example.rector.php](example.rector.php).
In addition, this solution uses Matt Glaman's awesome [mglaman/drupal-static-autoloader](https://github.com/mglaman/drupal-static-autoloader)
to cast all magic that is needed to autoload every necessary Drupal files.

### TODOs

- [ ] Add test coverage
- [ ] Add code-quality checks and linters

### Credits

* Thanks [Pronovix](https://pronovix.com) for sponsoring the initial development
* Kudos to all developers that this small library depends on
