[![Statamic 3.0+](https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge&link=https://statamic.com)](https://statamic.com/addons/visuellverstehen/classify)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/visuellverstehen/statamic-classify.svg?style=for-the-badge)](https://packagist.org/packages/visuellverstehen/statamic-classify)

# Classify
Classify is a useful helper to add CSS classes to your HTML tags, created by the Statamic 3 [Bard](https://statamic.dev/fieldtypes/bard) editor. 

# Installation

## Requirements

- Statamic v3
- Laravel 8
- PHP >= 7.4

### Version Compatibility

| Classify  | Statamic | Laravel
|:----------|:---------|:---------
| ^1.0      |  3.0     |  ^7.0
| ^2.0      |  3.0     |  ^8.0
| ^2.1      |  3.1     |  ^8.0
| ^2.2      |  3.1     |  ^8.0

## Installation

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
    'a'  => 'link hover:text-blue',
    'p' => 'mb-5',
    'li p' => 'mb-2 ml-4',
],
```
*This example uses TailwindCSS, but you can use whater kind of CSS you want.*

### Example Output
```html
<h1 class="text-2xl">A headline</h1>
<p class="mb-5">Some text</p>

<ul>
    <li><p class="mb-2 ml-4">A list item</p></li>
    <li><p class="mb-2 ml-4">A list item</p></li>
    <li><p class="mb-2 ml-4"><a class="link hover:text-blue" href="#">Klick me</a></p></li>
</ul>

<p class="mb-5">Another text</p>
```

### Usage

If you want to use the default style set, use the `classify` modifier. 
```php
{{ bard_text | classify }}
```

If you want to add multiple style sets, define those in the config file. For example:
```php
'default' => [
    // 
],
'blog' => [
    'a'  => 'link hover:underline',
    'li p' => 'ml-3 font-bold',
    'p' => 'mb-5',
],

// Antlers view
{{ bard_text | classify:blog }}
```

## Nested selectors (min version 2.2)
You can nest your selectors to flexible style elements in different contexts. 

Let's assume you want to style links inside lists differently than a general link, you can make use of nesting:
```php
`ul li p a` => 'my-secial-css-class-for-nested-links-in-lists`,
`a` => `a-general-link-class`,
```

The ordering does not matter. Classify will take care of that for you.


# More about us
- [www.statamic-agency.com](https://statamic-agency.com)
- [www.visuellverstehen.de](https://visuellverstehen.de)

# License
The MIT License (MIT). Please take a look at our [License File](LICENSE.md) for more information.
