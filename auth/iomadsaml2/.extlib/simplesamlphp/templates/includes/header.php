<?php

/**
 * Support the htmlinject hook, which allows modules to change header, pre and post body on all pages.
 */
$this->data['htmlinject'] = [
    'htmlContentPre' => [],
    'htmlContentPost' => [],
    'htmlContentHead' => [],
];

$jquery = [];
if (array_key_exists('jquery', $this->data)) {
    $jquery = $this->data['jquery'];
}

if (array_key_exists('pageid', $this->data)) {
    $hookinfo = [
        'pre' => &$this->data['htmlinject']['htmlContentPre'],
        'post' => &$this->data['htmlinject']['htmlContentPost'],
        'head' => &$this->data['htmlinject']['htmlContentHead'],
        'jquery' => &$jquery,
        'page' => $this->data['pageid']
    ];

    SimpleSAML\Module::callHooks('htmlinject', $hookinfo);
}
// - o - o - o - o - o - o - o - o - o - o - o - o -

/**
 * Do not allow to frame SimpleSAMLphp pages from another location.
 * This prevents clickjacking attacks in modern browsers.
 *
 * If you don't want any framing at all you can even change this to
 * 'DENY', or comment it out if you actually want to allow foreign
 * sites to put SimpleSAMLphp in a frame. The latter is however
 * probably not a good security practice.
 */
header('X-Frame-Options: SAMEORIGIN');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="initial-scale=1.0" />
<script type="text/javascript" src="/<?php echo $this->data['baseurlpath']; ?>resources/script.js"></script>
<title><?php
if (array_key_exists('header', $this->data)) {
    echo $this->data['header'];
} else {
    echo 'SimpleSAMLphp';
}
?></title>

    <link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/default.css" />
    <link rel="icon" type="image/icon" href="/<?php echo $this->data['baseurlpath']; ?>resources/icons/favicon.ico" />

<?php

if (!empty($jquery)) {
    $version = '1.8';
    if (array_key_exists('version', $jquery)) {
        $version = $jquery['version'];
    }

    if ($version == '1.8') {
        if (isset($jquery['core']) && $jquery['core']) {
            echo '<script type="text/javascript" src="/'.$this->data['baseurlpath'].'resources/jquery-1.8.js"></script>'."\n";
        }

        if (isset($jquery['ui']) && $jquery['ui']) {
            echo '<script type="text/javascript" src="/'.$this->data['baseurlpath'].'resources/jquery-ui-1.8.js"></script>'."\n";
        }

        if (isset($jquery['css']) && $jquery['css']) {
            echo '<link rel="stylesheet" media="screen" type="text/css" href="/'.$this->data['baseurlpath'].
                'resources/uitheme1.8/jquery-ui.css" />'."\n";
        }
    }
}

if (isset($this->data['clipboard.js'])) {
    echo '<script type="text/javascript" src="/'.$this->data['baseurlpath'].'resources/clipboard.min.js"></script>'."\n";
}

if (!empty($this->data['htmlinject']['htmlContentHead'])) {
    foreach ($this->data['htmlinject']['htmlContentHead'] as $c) {
        echo $c;
    }
}

if ($this->isLanguageRTL()) {
    ?>
    <link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/default-rtl.css" />
<?php
}
?>
    <meta name="robots" content="noindex, nofollow" />

<?php
if (array_key_exists('head', $this->data)) {
    echo '<!-- head -->'.$this->data['head'].'<!-- /head -->';
}
?>
</head>
<?php
$onLoad = '';
if (array_key_exists('autofocus', $this->data)) {
    $onLoad .= ' onload="SimpleSAML_focus(\''.$this->data['autofocus'].'\');"';
}
?>
<body<?php echo $onLoad; ?>>

<div id="wrap">

    <div id="header">
        <h1><a href="/<?php echo $this->data['baseurlpath']; ?>"><?php
            echo(isset($this->data['header']) ? $this->data['header'] : 'SimpleSAMLphp');
        ?></a></h1>
    </div>


    <?php

    $includeLanguageBar = true;
    if (!empty($_POST)) {
        $includeLanguageBar = false;
    }
    if (isset($this->data['hideLanguageBar']) && $this->data['hideLanguageBar'] === true) {
        $includeLanguageBar = false;
    }

    if ($includeLanguageBar) {
        $languages = $this->getLanguageList();
        ksort($languages);
        if (count($languages) > 1) {
            echo '<div id="languagebar">';
            $langnames = [
                'no' => 'Bokmål', // Norwegian Bokmål
                'nn' => 'Nynorsk', // Norwegian Nynorsk
                'se' => 'Sámegiella', // Northern Sami
                'da' => 'Dansk', // Danish
                'en' => 'English',
                'de' => 'Deutsch', // German
                'sv' => 'Svenska', // Swedish
                'fi' => 'Suomeksi', // Finnish
                'es' => 'Español', // Spanish
                'ca' => 'Català', // Catalan
                'fr' => 'Français', // French
                'it' => 'Italiano', // Italian
                'nl' => 'Nederlands', // Dutch
                'lb' => 'Lëtzebuergesch', // Luxembourgish
                'cs' => 'Čeština', // Czech
                'sl' => 'Slovenščina', // Slovensk
                'lt' => 'Lietuvių kalba', // Lithuanian
                'hr' => 'Hrvatski', // Croatian
                'hu' => 'Magyar', // Hungarian
                'pl' => 'Język polski', // Polish
                'pt' => 'Português', // Portuguese
                'pt-br' => 'Português brasileiro', // Portuguese
                'ru' => 'русский язык', // Russian
                'et' => 'eesti keel', // Estonian
                'tr' => 'Türkçe', // Turkish
                'el' => 'ελληνικά', // Greek
                'ja' => '日本語', // Japanese
                'zh' => '简体中文', // Chinese (simplified)
                'zh-tw' => '繁體中文', // Chinese (traditional)
                'ar' => 'العربية', // Arabic
                'he' => 'עִבְרִית', // Hebrew
                'id' => 'Bahasa Indonesia', // Indonesian
                'sr' => 'Srpski', // Serbian
                'lv' => 'Latviešu', // Latvian
                'ro' => 'Românește', // Romanian
                'eu' => 'Euskara', // Basque
                'af' => 'Afrikaans', // Afrikaans
                'zu' => 'IsiZulu', // Zulu
                'xh' => 'isiXhosa', // Xhosa
                'st' => 'Sesotho', // Sesotho
            ];

            $textarray = [];
            foreach ($languages as $lang => $current) {
                $lang = strtolower($lang);
                if ($current) {
                    $textarray[] = $langnames[$lang];
                } else {
                    $textarray[] = '<a href="'.htmlspecialchars(
                        \SimpleSAML\Utils\HTTP::addURLParameters(
                            \SimpleSAML\Utils\HTTP::getSelfURL(),
                            [$this->getTranslator()->getLanguage()->getLanguageParameterName() => $lang]
                        )
                    ).'">'.$langnames[$lang].'</a>';
                }
            }
            echo join(' | ', $textarray);
            echo '</div>';
        }
    }

    ?>
    <div id="content">

<?php

if (!empty($this->data['htmlinject']['htmlContentPre'])) {
    foreach ($this->data['htmlinject']['htmlContentPre'] as $c) {
        echo $c;
    }
}
$config = \SimpleSAML\Configuration::getInstance();
if(! $config->getBoolean('production', true)) {
    echo '<div class="caution">' . $this->t('{preprodwarning:warning:warning}'). '</div>';
}
