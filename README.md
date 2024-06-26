[![Statamic 4](https://img.shields.io/badge/Statamic-v4-FF269E?style=for-the-badge&link=https://statamic.com)](https://statamic.com/addons/visuellverstehen/classify) 
[![Statamic 5](https://img.shields.io/badge/Statamic-v5-FF269E?style=for-the-badge&link=https://statamic.com)](https://statamic.com/addons/visuellverstehen/classify) 
[![Latest version on Packagist](https://img.shields.io/packagist/v/visuellverstehen/statamic-classify.svg?style=for-the-badge)](https://packagist.org/packages/visuellverstehen/statamic-classify)

# Classify
Classify is a useful helper to add CSS classes to all HTML tags generated by the Statamic [Bard](https://statamic.dev/fieldtypes/bard) editor. 

# Installation

## Requirements

- Statamic v4 || v5
- Laravel 10 || 11
- PHP 8.2+

## Installation

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
*This example uses TailwindCSS, but you can use whatever kind of CSS you want.*

### Example Output
```html
<h1 class="text-2xl">A headline</h1>
<p class="mb-5">Some text</p>

<ul>
    <li><p class="mb-2 ml-4">A list item</p></li>
    <li><p class="mb-2 ml-4">A list item</p></li>
    <li><p class="mb-2 ml-4"><a class="link hover:text-blue" href="#">Click me</a></p></li>
</ul>

<p class="mb-5">Another text</p>
```

### Usage

If you want to use the default style set, use the `classify` modifier. 
```php
{{ bard_text | classify }}
```

If you want to add multiple style sets, you can define those in the config file:
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

If you want to get the defined classes for a certain element, you can get them straight from your config or use the `classify` tag:
```
{{ classify:h1 }}
// output: text-2xl

{{ classify element="li p" }}
// output: mb-2 ml-4

{{ classify:a styleset="blog" }}
// output: link hover:underline
```

## Nested selectors
You can nest your selectors to style elements differently in varying contexts. 

Let's assume you want to style links inside lists differently to a general link, you can make use of nesting:

```php
`ul li p a` => 'my-secial-css-class-for-nested-links-in-lists`,
`a` => `a-general-link-class`,
```

Classify will take care of the order, so you don't need to define the nested selector before the general selector.

## Working with CSS frameworks (like TailwindCSS)

Some CSS frameworks utilize JIT compiling, or have some other means of purging CSS classes from production builds to reduce file size. Your classify CSS classes may not appear anywhere else in your template files, as they will be added dynamically. To make sure that your classes will be considered for compiling, you have to include the classify config file as content:

```js
module.exports = {
  content: [
    // all of your content files
    // './pages/**/*.{html,js}'
    // './components/**/*.{html,js}',
    './config/classify.php',
  ],
}
```

# More about us
- [www.statamic-agency.com](https://statamic-agency.com)
- [www.visuellverstehen.de](https://visuellverstehen.de)

# License
The MIT License (MIT). Please have a look at our [license file](LICENSE.md) for more information.
