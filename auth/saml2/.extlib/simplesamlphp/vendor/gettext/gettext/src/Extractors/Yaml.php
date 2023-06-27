<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class to get gettext strings from yaml.
 */
class Yaml extends Extractor implements ExtractorInterface
{
    use MultidimensionalArrayTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $messages = YamlParser::parse($string);

        if (is_array($messages)) {
            static::fromArray($messages, $translations);
        }
    }
}
