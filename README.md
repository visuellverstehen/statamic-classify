![Statamic 3.0+](https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge&link=https://statamic.com)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/visuellverstehen/statamic-classify.svg?style=for-the-badge)](https://packagist.org/packages/jonassiewertsen/statamic-butik)

# Classify
Classify is a useful helper to add CSS Classes to all of your HTML tags, created by the [bard](https://statamic.dev/fieldtypes/bard) editor. 

## What it does
Bard's default output would be:
```
<h1>A nice headline</h1>
<p>Something really <a href="#">important</a></p>
```

Would you like to add some classes to different HTML tags?
```
<h1 class="text-2xl">A nice headline</h1>
<p>Something really <a class="link hover:blue" href="#">important</a></p>
```

In that case, Classfiy will be your friend.

## Installation

1. Install Classify from the `Tools > Addons` section of your control panel, or via composer:

```
composer require visuellverstehen/statamic-classify
```

## Publish config file

The installation process will automatically publish the `classify.php` file into the `config` folder.

## Usage

### Configuration
To add or change style sets, simply add or change an array with classes that should be added to the HTML Tag.
```
'default' => [
        'h1' => 'text-2xl',
        'a'  => 'link hover:blue',
    ],
```

### Usage

If you want to use the default style set, use the `classify` modifier. 
```
{{ bard_text | classify }}
```

If you want to add multiple style sets, define those in the config file. For example:
```
// config
'blog' => [
    'button' => 'button button--blue',
    'a'  => 'link',
],

// Antlers view
{{ bard_text | classify:blog }}
```

# License
The MIT License (MIT). Please see our [License File](LICENSE.md) for more information.

