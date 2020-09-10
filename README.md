[![Statamic 3.0+](https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge&link=https://statamic.com)](https://statamic.com/addons/visuellverstehen/classify)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/visuellverstehen/statamic-classify.svg?style=for-the-badge)](https://packagist.org/packages/visuellverstehen/statamic-classify)

# Classify
Classify is a useful helper to add CSS classes to all of your HTML tags, created by the [Bard](https://statamic.dev/fieldtypes/bard) editor. 

## What it does
Bards default output would be:
```html
<h1>A nice headline</h1>
<p>Something really <a href="#">important</a></p>
```

Would you like to add some classes to different HTML tags?
```html
<h1 class="text-2xl">A nice headline</h1>
<p>Something really <a class="link hover:blue" href="#">important</a></p>
```

In that case, Classify will be your friend.

# Installation

## Requirements

- Statamic v3
- Laravel 7
- PHP >= 7.4

## Install addon

There are two ways to install the Classify addon.

### Control panel

Install Classify from the `Tools > Addons` section of your control panel.

### Composer

Install Classify via composer:

```bash
composer require visuellverstehen/statamic-classify
```

## Publish config file

The installation process will automatically publish the `classify.php` file into the `config` folder.

## Usage

### Configuration
To add or change style sets, simply add or change an array with classes that should be added to the HTML tag.
```php
'default' => [
        'h1' => 'text-2xl',
        'a'  => 'link hover:blue',
    ],
```

### Usage

If you want to use the default style set, use the `classify` modifier. 
```php
{{ bard_text | classify }}
```

If you want to add multiple style sets, define those in the config file. For example:
```php
// config
'blog' => [
    'button' => 'button button--blue',
    'a'  => 'link',
],

// Antlers view
{{ bard_text | classify:blog }}
```

# License
The MIT License (MIT). Please take a look at our [License File](LICENSE.md) for more information.

# More about us
- [www.statamic-agency.com](https://statamic-agency.com)
- [www.visuellverstehen.de](https://visuellverstehen.de)


