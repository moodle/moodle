# PDF parser

[![Version](https://poser.pugx.org/smalot/pdfparser/v)](//packagist.org/packages/smalot/pdfparser)
![CI](https://github.com/smalot/pdfparser/workflows/CI/badge.svg)
![CS](https://github.com/smalot/pdfparser/workflows/CS/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smalot/pdfparser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smalot/pdfparser/?branch=master)
[![Downloads](https://poser.pugx.org/smalot/pdfparser/downloads)](//packagist.org/packages/smalot/pdfparser)

The `smalot/pdfparser` is a standalone PHP package that provides various tools to extract data from PDF files.

This library is under **active maintenance**.
There is no active development by the author of this library (at the moment), but we welcome any pull request adding/extending functionality!
See [CONTRIBUTING.md](./CONTRIBUTING.md) for further information about how to contribute.

## Features

- Load/parse objects and headers
- Extract metadata (author, description, ...)
- Extract text from ordered pages
- Support of compressed PDFs
- Support of MAC OS Roman charset encoding
- Handling of hexa and octal encoding in text sections
- Create custom configurations (see [CustomConfig.md](/doc/CustomConfig.md)).

Currently, secured documents and extracting form data are not supported.

## License

This library is under the [LGPLv3 license](https://github.com/smalot/pdfparser/blob/master/LICENSE.txt).

## Install

This library requires PHP 7.1+ since [v1](https://github.com/smalot/pdfparser/releases/tag/v1.0.0).
You can install it via [Composer](https://getcomposer.org/):

```bash
composer require smalot/pdfparser
```

In case you can't use Composer, you can include `alt_autoload.php-dist`. It will include all required files automatically.

## Quick example

```php
<?php

// Parse PDF file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('/path/to/document.pdf');

$text = $pdf->getText();
echo $text;
```

Further usage information can be found [here](/doc/Usage.md).

## Documentation

Documentation can be found in the [doc](/doc) folder.
