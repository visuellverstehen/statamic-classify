## Installation

1. Install Classify from the `Tools > Addons` section of your control panel, or via composer:

```
composer require visuellverstehen/statamic-classify
```
## Configuration

After installation there will be a [classify.php](config/classify.php) file in the `config` folder.
To add or change stylesets simply add or change an array with classes that should be added to the HTML Tag.

## Usage

To apply the classes to the HTML Tags you have to pipe the content through the modifier.

If you don't give a special styleset the default styleset will be applied.
```
{{ text | classify }}
```

If you want to apply a special styleset wich is named `blog` you have to use the following.

```
{{ text | classify:blog }}
```

If you provide a styleset wich is not configured you will get the HTML Tag back without any changes.
