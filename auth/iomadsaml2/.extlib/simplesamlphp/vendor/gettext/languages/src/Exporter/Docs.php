<?php

namespace Gettext\Languages\Exporter;

class Docs extends Html
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::isForPublicUse()
     */
    public static function isForPublicUse()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build the page https://php-gettext.github.io/Languages/';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::toStringDo()
     */
    protected static function toStringDo($languages)
    {
        $result = <<<'EOT'
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Michele Locati">
        <title>gettext plural rules - built from CLDR</title>
        <meta name="description" content="List of all language rules for gettext .po files automatically generated from the Unicode CLDR data" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <a href="https://github.com/php-gettext/Languages" class="hidden-xs"><img style="position: fixed; top: 0; right: 0; border: 0; z-index: 2000" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>
        <div class="container-fluid">

EOT;
        $result .= static::buildTable($languages, true);
        $result .= <<<'EOT'

        </div>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="script.js"></script>
    </body>
</html>
EOT;

        return $result;
    }
}
