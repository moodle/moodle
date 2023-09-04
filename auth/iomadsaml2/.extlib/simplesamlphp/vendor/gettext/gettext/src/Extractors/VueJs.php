<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Gettext\Extractors;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;
use Gettext\Translations;
use Gettext\Utils\FunctionsScanner;

/**
 * Class to get gettext strings from VueJS template files.
 */
class VueJs extends Extractor implements ExtractorInterface, ExtractorMultiInterface
{
    public static $options = [
        'constants' => [],

        'functions' => [
            'gettext' => 'gettext',
            '__' => 'gettext',
            'ngettext' => 'ngettext',
            'n__' => 'ngettext',
            'pgettext' => 'pgettext',
            'p__' => 'pgettext',
            'dgettext' => 'dgettext',
            'd__' => 'dgettext',
            'dngettext' => 'dngettext',
            'dn__' => 'dngettext',
            'dpgettext' => 'dpgettext',
            'dp__' => 'dpgettext',
            'npgettext' => 'npgettext',
            'np__' => 'npgettext',
            'dnpgettext' => 'dnpgettext',
            'dnp__' => 'dnpgettext',
            'noop' => 'noop',
            'noop__' => 'noop',
        ],
    ];

    protected static $functionsScannerClass = 'Gettext\Utils\JsFunctionsScanner';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromFileMultiple($file, array $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            $options['file'] = $file;
            static::fromStringMultiple(static::readFile($file), $translations, $options);
        }
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        static::fromStringMultiple($string, [$translations], $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromStringMultiple($string, array $translations, array $options = [])
    {
        $options += static::$options;
        $options += [
            // HTML attribute prefixes we parse as JS which could contain translations (are JS expressions)
            'attributePrefixes' => [
                ':',
                'v-bind:',
                'v-on:',
                'v-text',
            ],
            // HTML Tags to parse
            'tagNames' => [
                'translate',
            ],
            // HTML tags to parse when attribute exists
            'tagAttributes' => [
                'v-translate',
            ],
            // Comments
            'commentAttributes' => [
                'translate-comment',
            ],
            'contextAttributes' => [
                'translate-context',
            ],
            // Attribute with plural content
            'pluralAttributes' => [
                'translate-plural',
            ],
        ];

        // Ok, this is the weirdest hack, but let me explain:
        // On Linux (Mac is fine), when converting HTML to DOM, new lines get trimmed after the first tag.
        // So if there are new lines between <template> and next element, they are lost
        // So we insert a "." which is a text node, and it will prevent that newlines are stripped between elements.
        // Same thing happens between template and script tag.
        $string = str_replace('<template>', '<template>.', $string);
        $string = str_replace('</template>', '</template>.', $string);

        // Normalize newlines
        $string = str_replace(["\r\n", "\n\r", "\r"], "\n", $string);

        // VueJS files are valid HTML files, we will operate with the DOM here
        $dom = static::convertHtmlToDom($string);

        $script = static::extractScriptTag($string);

        // Parse the script part as a regular JS code
        if ($script) {
            $scriptLineNumber = $dom->getElementsByTagName('script')->item(0)->getLineNo();
            static::getScriptTranslationsFromString(
                $script,
                $translations,
                $options,
                $scriptLineNumber - 1
            );
        }

        // Template part is parsed separately, all variables will be extracted
        // and handled as a regular JS code
        $template = $dom->getElementsByTagName('template')->item(0);
        if ($template) {
            static::getTemplateTranslations(
                $template,
                $translations,
                $options,
                $template->getLineNo() - 1
            );
        }
    }

    /**
     * Extracts script tag contents using regex instead of DOM operations.
     * If we parse using DOM, some contents may change, for example, tags within strings will be stripped
     *
     * @param $string
     * @return bool|string
     */
    protected static function extractScriptTag($string)
    {
        if (preg_match('#<\s*?script\b[^>]*>(.*?)</script\b[^>]*>#s', $string, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param string $html
     * @return DOMDocument
     */
    protected static function convertHtmlToDom($html)
    {
        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        // Prepend xml encoding so DOMDocument document handles UTF8 correctly.
        // Assuming that vue template files will not have any xml encoding tags, because duplicate tags may be ignored.
        $dom->loadHTML('<?xml encoding="utf-8"?>' . $html);

        libxml_clear_errors();

        return $dom;
    }

    /**
     * Extract translations from script part
     *
     * @param string $scriptContents Only script tag contents, not the whole template
     * @param Translations|Translations[] $translations One or multiple domain Translation objects
     * @param array $options
     * @param int $lineOffset Number of lines the script is offset in the vue template file
     * @throws Exception
     */
    protected static function getScriptTranslationsFromString(
        $scriptContents,
        $translations,
        array $options = [],
        $lineOffset = 0
    ) {
        /** @var FunctionsScanner $functions */
        $functions = new static::$functionsScannerClass($scriptContents);
        $options['lineOffset'] = $lineOffset;
        $functions->saveGettextFunctions($translations, $options);
    }

    /**
     * Parse template to extract all translations (element content and dynamic element attributes)
     *
     * @param DOMNode $dom
     * @param Translations|Translations[] $translations One or multiple domain Translation objects
     * @param array $options
     * @param int $lineOffset Line number where the template part starts in the vue file
     * @throws Exception
     */
    protected static function getTemplateTranslations(
        DOMNode $dom,
        $translations,
        array $options,
        $lineOffset = 0
    ) {
        // Build a JS string from all template attribute expressions
        $fakeAttributeJs = static::getTemplateAttributeFakeJs($options, $dom);

        // 1 line offset is necessary because parent template element was ignored when converting to DOM
        static::getScriptTranslationsFromString($fakeAttributeJs, $translations, $options, $lineOffset);

        // Build a JS string from template element content expressions
        $fakeTemplateJs = static::getTemplateFakeJs($dom);
        static::getScriptTranslationsFromString($fakeTemplateJs, $translations, $options, $lineOffset);

        static::getTagTranslations($options, $dom, $translations);
    }

    /**
     * @param array $options
     * @param DOMNode $dom
     * @param Translations|Translations[] $translations
     */
    protected static function getTagTranslations(array $options, DOMNode $dom, $translations)
    {
        // Since tag scanning does not support domains, we always use the first translation given
        $translations = is_array($translations) ? reset($translations) : $translations;

        $children = $dom->childNodes;
        for ($i = 0; $i < $children->length; $i++) {
            $node = $children->item($i);

            if (!($node instanceof DOMElement)) {
                continue;
            }

            $translatable = false;

            if (in_array($node->tagName, $options['tagNames'], true)) {
                $translatable = true;
            }

            $attrList = $node->attributes;
            $context = null;
            $plural = "";
            $comment = null;

            for ($j = 0; $j < $attrList->length; $j++) {
                /** @var DOMAttr $domAttr */
                $domAttr = $attrList->item($j);
                // Check if this is a dynamic vue attribute
                if (in_array($domAttr->name, $options['tagAttributes'])) {
                    $translatable = true;
                }
                if (in_array($domAttr->name, $options['contextAttributes'])) {
                    $context = $domAttr->value;
                }
                if (in_array($domAttr->name, $options['pluralAttributes'])) {
                    $plural = $domAttr->value;
                }
                if (in_array($domAttr->name, $options['commentAttributes'])) {
                    $comment = $domAttr->value;
                }
            }

            if ($translatable) {
                $translation = $translations->insert($context, trim($node->textContent), $plural);
                $translation->addReference($options['file'], $node->getLineNo());
                if ($comment) {
                    $translation->addExtractedComment($comment);
                }
            }

            if ($node->hasChildNodes()) {
                static::getTagTranslations($options, $node, $translations);
            }
        }
    }

    /**
     * Extract JS expressions from element attribute bindings (excluding text within elements)
     * For example: <span :title="__('extract this')"> skip element content </span>
     *
     * @param array $options
     * @param DOMNode $dom
     * @return string JS code
     */
    protected static function getTemplateAttributeFakeJs(array $options, DOMNode $dom)
    {
        $expressionsByLine = static::getVueAttributeExpressions($options['attributePrefixes'], $dom);

        if (empty($expressionsByLine)) {
            return '';
        }

        $maxLines = max(array_keys($expressionsByLine));
        $fakeJs = '';

        for ($line = 1; $line <= $maxLines; $line++) {
            if (isset($expressionsByLine[$line])) {
                $fakeJs .= implode("; ", $expressionsByLine[$line]);
            }
            $fakeJs .= "\n";
        }

        return $fakeJs;
    }

    /**
     * Loop DOM element recursively and parse out all dynamic vue attributes which are basically JS expressions
     *
     * @param array $attributePrefixes List of attribute prefixes we parse as JS (may contain translations)
     * @param DOMNode $dom
     * @param array $expressionByLine [lineNumber => [jsExpression, ..], ..]
     * @return array [lineNumber => [jsExpression, ..], ..]
     */
    protected static function getVueAttributeExpressions(
        array $attributePrefixes,
        DOMNode $dom,
        array &$expressionByLine = []
    ) {
        $children = $dom->childNodes;

        for ($i = 0; $i < $children->length; $i++) {
            $node = $children->item($i);

            if (!($node instanceof DOMElement)) {
                continue;
            }
            $attrList = $node->attributes;

            for ($j = 0; $j < $attrList->length; $j++) {
                /** @var DOMAttr $domAttr */
                $domAttr = $attrList->item($j);

                // Check if this is a dynamic vue attribute
                if (static::isAttributeMatching($domAttr->name, $attributePrefixes)) {
                    $line = $domAttr->getLineNo();
                    $expressionByLine += [$line => []];
                    $expressionByLine[$line][] = $domAttr->value;
                }
            }

            if ($node->hasChildNodes()) {
                $expressionByLine = static::getVueAttributeExpressions($attributePrefixes, $node, $expressionByLine);
            }
        }

        return $expressionByLine;
    }

    /**
     * Check if this attribute name should be parsed for translations
     *
     * @param string $attributeName
     * @param string[] $attributePrefixes
     * @return bool
     */
    protected static function isAttributeMatching($attributeName, $attributePrefixes)
    {
        foreach ($attributePrefixes as $prefix) {
            if (strpos($attributeName, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract JS expressions from within template elements (excluding attributes)
     * For example: <span :title="skip attributes"> {{__("extract element content")}} </span>
     *
     * @param DOMNode $dom
     * @return string JS code
     */
    protected static function getTemplateFakeJs(DOMNode $dom)
    {
        $fakeJs = '';
        $lines = explode("\n", $dom->textContent);

        // Build a fake JS file from template by extracting JS expressions within each template line
        foreach ($lines as $line) {
            $expressionMatched = static::parseOneTemplateLine($line);

            $fakeJs .= implode("; ", $expressionMatched) . "\n";
        }

        return $fakeJs;
    }

    /**
     * Match JS expressions in a template line
     *
     * @param string $line
     * @return string[]
     */
    protected static function parseOneTemplateLine($line)
    {
        $line = trim($line);

        if (!$line) {
            return [];
        }

        $regex = '#\{\{(.*?)\}\}#';

        preg_match_all($regex, $line, $matches);

        $matched = array_map(function ($v) {
            return trim($v, '\'"{}');
        }, $matches[1]);

        return $matched;
    }
}
