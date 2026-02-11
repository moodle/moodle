# Usage

First create a parser object and point it to a file.

```php
$parser = new \Smalot\PdfParser\Parser();

$pdf = $parser->parseFile('document.pdf');
// .. or ...
$pdf = $parser->parseContent(file_get_contents('document.pdf'))
 ```

## Extract text

A common scenario is to extract text.

```php
// extract text of the whole PDF
$text = $pdf->getText();

// or extract the text of a specific page (in this case the first page)
$text = $pdf->getPages()[0]->getText();

// you can also extract text of a limited amount of pages. here, it will only use the first five pages.
$text = $pdf->getText(5);
```

## Extract text positions

You can extract transformation matrix (indexes 0-3) and x,y position of text objects (indexes 4,5).

```php
$data = $pdf->getPages()[0]->getDataTm();

Array
(
    [0] => Array
        (
            [0] => Array
                (
                    [0] => 0.999429
                    [1] => 0
                    [2] => 0
                    [3] => 1
                    [4] => 201.96
                    [5] => 720.68
                )

            [1] => Document title
        )

    [1] => Array
        (
            [0] => Array
                (
                    [0] => 0.999402
                    [1] => 0
                    [2] => 0
                    [3] => 1
                    [4] => 70.8
                    [5] => 673.64
                )

            [1] => Calibri : Lorem ipsum dolor sit amet, consectetur a
        )
)
```

When activated via Config setting (`Config::setDataTmFontInfoHasToBeIncluded(true)`) font identifier (index 2) and font size (index 3) are added to dataTm.

```php
// create config
$config = new Smalot\PdfParser\Config();
$config->setDataTmFontInfoHasToBeIncluded(true);

// use config and parse file
$parser = new Smalot\PdfParser\Parser([], $config);
$pdf = $parser->parseFile('document.pdf');
$firstpage = $pdf->getPages()[0];
$data = $firstpage->getDataTm();

Array
(
    [0] => Array
        (
            [0] => Array
                (
                    [0] => 0.999429
                    [1] => 0
                    [2] => 0
                    [3] => 1
                    [4] => 201.96
                    [5] => 720.68
                )

            [1] => Document title
            [2] => R7
            [3] => 27.96
        )

    [1] => Array
        (
            [0] => Array
                (
                    [0] => 0.999402
                    [1] => 0
                    [2] => 0
                    [3] => 1
                    [4] => 70.8
                    [5] => 673.64
                )

            [1] => Calibri : Lorem ipsum dolor sit amet, consectetur a
            [2] => R9
            [3] => 11.04
        )
)
```

Text width should be calculated on text from dataTm to make sure all character widths are available.
In next example we are using data from above.

```php
$font_id = $data[0][2]; //R7
$font = $firstpage->getFont($font_id);
$text = $data[0][1];
$width = $font->calculateTextWidth($text, $missing);
```

## Extract metadata

You can also extract metadata. The available data varies from PDF to PDF.

```php
$metaData = $pdf->getDetails();

Array
(
    [Producer] => Adobe Acrobat
    [CreatedOn] => 2022-01-28T16:36:11+00:00
    [Pages] => 35
    ...
)
```

If the PDF contains Extensible Metadata Platform (XMP) XML metadata, their values, including the XMP namespace, will be appended to the data returned by `getDetails()`. You can read more about what values and namespaces are commonly used in the [XMP Specifications](https://github.com/adobe/XMP-Toolkit-SDK/tree/main/docs).

```php
Array
(
    ...
    [Pages] => 35
    [dc:creator] => My Name
    [pdf:producer] => Adobe Acrobat
    [dc:title] => My Document Title
    ...
)
```

Some XMP metadata values may have multiple values, or even named children with their own values. In these cases, the value will be an array. The XMP metadata will follow the structure of the XML so it is possible to have multiple levels of nested values.

```php
Array
(
    ...
    [dc:title] => My Document Title
    [xmptpg:maxpagesize] => Array
    (
        [stdim:w] => 21.500000
        [stdim:h] => 6.222222
        [stdim:unit] => Inches
    )
    [xmptpg:platenames] => Array
    (
        [0] => Cyan
        [1] => Magenta
        [2] => Yellow
        [3] => Black
    )
    ...
)
```


## Read Base64 encoded PDFs

If working with [Base64](https://en.wikipedia.org/wiki/Base64) encoded PDFs, you might want to parse the PDF without saving the file to disk.
This sample will parse the Base64 encoded PDF and extract text from each page.

```php
<?php
// Parse Base64 encoded PDF string and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseContent(base64_decode($base64PDF));

$text = $pdf->getText();
echo $text;
```

## Calculate text width

Try to calculate text width for given font.
Characters without width are added to `$missing` array in second parameter.

```php
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('document.pdf');
$fonts = $pdf->getFonts();
// get first font (we assume here there is at least one)
$font = reset($fonts);
// get width
$width = $font->calculateTextWidth('Some text', $missing);
```

## Get pages width and height

Ref: [#472](https://github.com/smalot/pdfparser/issues/427#issuecomment-973416786)

```php
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('document.pdf');
$pages = $pdf->getPages();
// this variable will contain the height and width of each page of the given PDF
$mediaBox = [];
foreach ($pages as $page) {
    $details = $page->getDetails();
    // If Mediabox is not set in details of current $page instance, get details from the header instead
    if (!isset($details['MediaBox'])) {
        $pages = $pdf->getObjectsByType('Pages');
        $details = reset($pages)->getHeader()->getDetails();
    }
    $mediaBox[] = [
        'width' => $details['MediaBox'][2],
        'height' => $details['MediaBox'][3]
    ];
}
```

## PDF encryption

This library cannot currently read encrypted PDF files, i.e. those with
a read password.  Attempting to do so produces this error:
```
Exception: Secured pdf file are currently not supported.
```

See `setIgnoreEncryption` option in [CustomConfig.md](CustomConfig.md)
for how to override the check in specific cases.
