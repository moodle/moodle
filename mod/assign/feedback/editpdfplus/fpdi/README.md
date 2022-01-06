FPDI - Free PDF Document Importer
=================================

[![Latest Stable Version](https://poser.pugx.org/setasign/fpdi/v/stable.svg)](https://packagist.org/packages/setasign/fpdi)
[![Total Downloads](https://poser.pugx.org/setasign/fpdi/downloads.svg)](https://packagist.org/packages/setasign/fpdi)
[![Latest Unstable Version](https://poser.pugx.org/setasign/fpdi/v/unstable.svg)](https://packagist.org/packages/setasign/fpdi)
[![License](https://poser.pugx.org/setasign/fpdi/license.svg)](https://packagist.org/packages/setasign/fpdi)
[![Build Status](https://travis-ci.org/Setasign/FPDI.svg?branch=development)](https://travis-ci.org/Setasign/FPDI)

:heavy_exclamation_mark: This document refers to FPDI 2. Version 1 is deprecated and development is discontinued. :heavy_exclamation_mark: 

FPDI is a collection of PHP classes facilitating developers to read pages from existing PDF
documents and use them as templates in [FPDF](http://www.fpdf.org), which was developed by Olivier Plathey. Apart
from a copy of [FPDF](http://www.fpdf.org), FPDI does not require any special PHP extensions.

FPDI can also be used as an extension for [TCPDF](https://github.com/tecnickcom/TCPDF) or 
[tFPDF](http://fpdf.org/en/script/script92.php), too.

## Installation with [Composer](https://packagist.org/packages/setasign/fpdi)

Because FPDI can be used with FPDF, TCPDF or tFPDF we didn't added a fixed dependency in the main
composer.json file but we added metadata packages for 
[FPDF](https://github.com/Setasign/FPDI-FPDF), 
[TCPDF](https://github.com/Setasign/FPDI-TCPDF) and
[tFPDF](https://github.com/Setasign/FPDI-tFPDF).

### Evaluate Dependencies Automatically

For FPDF add following [package](https://github.com/Setasign/FPDI-FPDF) to your composer.json:
```json
{
    "require": {
        "setasign/fpdi-fpdf": "^2.0"
    }
}
```

For TCPDF add following [package](https://github.com/Setasign/FPDI-TCPDF) to your composer.json:
```json
{
    "require": {
        "setasign/fpdi-tcpdf": "^2.0"
    }
}
```

For tFPDF add following [package](https://github.com/Setasign/FPDI-tFPDF) to your composer.json:
```json
{
    "require": {
        "setasign/fpdi-tfpdf": "^2.1"
    }
}
```

### Manual Dependencies

If you don't want to use the metadata packages, it is up to you to add the dependencies to your
composer.json file.

To use FPDI with FPDF include following in your composer.json file:

```json
{
    "require": {
        "setasign/fpdf": "^1.8",
        "setasign/fpdi": "^2.0"
    }
}
```

If you want to use TCPDF, your have to update your composer.json respectively to:

```json
{
    "require": {
        "tecnickcom/tcpdf": "^6.2",
        "setasign/fpdi": "^2.0"
    }
}
```

If you want to use tFPDF, your have to update your composer.json respectively to:

```json
{
    "require": {
        "tecnickcom/tfpdf": "1.25",
        "setasign/fpdi": "^2.1"
    }
}
```

## Manual Installation

If you do not use composer, just require the autoload.php in the /src folder:

```php
require_once('src/autoload.php');
```

If you have a PSR-4 autoloader implemented, just register the src path as follows:
```php
$loader = new \Example\Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('setasign\Fpdi', 'path/to/src/');
```

## Changes to Version 1

Version 2 is a complete rewrite from scratch of FPDI which comes with:
- Namespaced code
- Clean and up-to-date code base and style
- PSR-4 compatible autoloading
- Performance improvements by up to 100%
- Less memory consumption
- Native support for reading PDFs from strings or stream-resources
- Support for documents with "invalid" data before their file-header
- Optimized page tree resolving
- Usage of individual exceptions
- Several test types (unit, functional and visual tests)

We tried to keep the main methods and logical workflow the same as in version 1 but please
notice that there were incompatible changes which you should consider when updating to
version 2:
- You need to load the code using the `src/autoload.php` file instead of `classes/FPDI.php`.
- The classes and traits are namespaced now: `setasign\Fpdi`
- Page boundaries beginning with a slash, such as `/MediaBox`, are not supported anymore. Remove
  the slash or use a constant of `PdfReader\PageBoundaries`.
- The parameters $x, $y, $width and $height of the `useTemplate()` or `getTemplateSize()`
  method have more logical correct default values now. Passing `0` as width or height will
  result in an `InvalidArgumentException` now.
- The return value of `getTemplateSize()` had changed to an array with more speaking keys
  and reusability: Use `width` instead of `w` and `height` instead of `h`.
- If you want to use **FPDI with TCPDF** you need to refactor your code to use the class `Tcpdf\Fpdi`
(since 2.1; before it was `TcpdfFpdi`) instead of `FPDI`.

## Example and Documentation

A simple example, that imports a single page and places this onto a new created page:

```php
<?php
use setasign\Fpdi\Fpdi;
// or for usage with TCPDF:
// use setasign\Fpdi\Tcpdf\Fpdi;

// or for usage with tFPDF:
// use setasign\Fpdi\Tfpdf\Fpdi;

// setup the autoload function
require_once('vendor/autoload.php');

// initiate FPDI
$pdf = new Fpdi();
// add a page
$pdf->AddPage();
// set the source file
$pdf->setSourceFile("Fantastic-Speaker.pdf");
// import page 1
$tplId = $pdf->importPage(1);
// use the imported page and place it at point 10,10 with a width of 100 mm
$pdf->useTemplate($tplId, 10, 10, 100);

$pdf->Output();            
```

A full end-user documentation and API reference is available [here](https://manuals.setasign.com/fpdi-manual/).