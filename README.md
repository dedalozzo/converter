Converter
=========
A set of classes to translate a text from a HTML to BBcode and from BBCode to Markdown.


Composer Installation
---------------------

To install Converter, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


Converter Installation
----------------------
Once you have installed Composer, it's easy install Converter.

1. Edit your `composer.json` file, adding Converter to the require section:
```sh
{
    "require": {
        "3f/converter": "dev-master"
    },
}
```
2. Run the following command in your project root dir:
```sh
composer update
```


Usage
-----
There are two classes: `HTMLConverter` and `BBCodeConverter()`. The first class may be used to convert from HTML to
BBCode, while the second one is used to convert from BBCode to Markdown.

HTML to BBCode conversion:

```php
$converter = new Converter\HTMLConverter($text, $id);
echo $converter->toBBCode();
```

BBCode to Markdown conversion:

```php
$converter = new Converter\BBCodeConverter($text, $id);
echo $converter->toMarkdown();
```


Documentation
-------------
The documentation can be generated using [Doxygen](http://doxygen.org). A `Doxyfile` is provided for your convenience.


Requirements
------------
- PHP 5.4.0 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


License
-------
Converter is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.