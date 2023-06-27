<?php

namespace Gettext;

use Gettext\Languages\Language;
use BadMethodCallException;
use InvalidArgumentException;
use ArrayObject;

/**
 * Class to manage a collection of translations.
 *
 * @method static $this fromBladeFile(string $filename, array $options = [])
 * @method static $this fromBladeString(string $string, array $options = [])
 * @method $this addFromBladeFile(string $filename, array $options = [])
 * @method $this addFromBladeString(string $string, array $options = [])
 * @method static $this fromCsvFile(string $filename, array $options = [])
 * @method static $this fromCsvString(string $string, array $options = [])
 * @method $this addFromCsvFile(string $filename, array $options = [])
 * @method $this addFromCsvString(string $string, array $options = [])
 * @method bool toCsvFile(string $filename, array $options = [])
 * @method string toCsvString(array $options = [])
 * @method static $this fromCsvDictionaryFile(string $filename, array $options = [])
 * @method static $this fromCsvDictionaryString(string $string, array $options = [])
 * @method $this addFromCsvDictionaryFile(string $filename, array $options = [])
 * @method $this addFromCsvDictionaryString(string $string, array $options = [])
 * @method bool toCsvDictionaryFile(string $filename, array $options = [])
 * @method string toCsvDictionaryString(array $options = [])
 * @method static $this fromJedFile(string $filename, array $options = [])
 * @method static $this fromJedString(string $string, array $options = [])
 * @method $this addFromJedFile(string $filename, array $options = [])
 * @method $this addFromJedString(string $string, array $options = [])
 * @method bool toJedFile(string $filename, array $options = [])
 * @method string toJedString(array $options = [])
 * @method static $this fromJsCodeFile(string $filename, array $options = [])
 * @method static $this fromJsCodeString(string $string, array $options = [])
 * @method $this addFromJsCodeFile(string $filename, array $options = [])
 * @method $this addFromJsCodeString(string $string, array $options = [])
 * @method static $this fromJsonFile(string $filename, array $options = [])
 * @method static $this fromJsonString(string $string, array $options = [])
 * @method $this addFromJsonFile(string $filename, array $options = [])
 * @method $this addFromJsonString(string $string, array $options = [])
 * @method bool toJsonFile(string $filename, array $options = [])
 * @method string toJsonString(array $options = [])
 * @method static $this fromJsonDictionaryFile(string $filename, array $options = [])
 * @method static $this fromJsonDictionaryString(string $string, array $options = [])
 * @method $this addFromJsonDictionaryFile(string $filename, array $options = [])
 * @method $this addFromJsonDictionaryString(string $string, array $options = [])
 * @method bool toJsonDictionaryFile(string $filename, array $options = [])
 * @method string toJsonDictionaryString(array $options = [])
 * @method static $this fromMoFile(string $filename, array $options = [])
 * @method static $this fromMoString(string $string, array $options = [])
 * @method $this addFromMoFile(string $filename, array $options = [])
 * @method $this addFromMoString(string $string, array $options = [])
 * @method bool toMoFile(string $filename, array $options = [])
 * @method string toMoString(array $options = [])
 * @method static $this fromPhpArrayFile(string $filename, array $options = [])
 * @method static $this fromPhpArrayString(string $string, array $options = [])
 * @method $this addFromPhpArrayFile(string $filename, array $options = [])
 * @method $this addFromPhpArrayString(string $string, array $options = [])
 * @method bool toPhpArrayFile(string $filename, array $options = [])
 * @method string toPhpArrayString(array $options = [])
 * @method static $this fromPhpCodeFile(string $filename, array $options = [])
 * @method static $this fromPhpCodeString(string $string, array $options = [])
 * @method $this addFromPhpCodeFile(string $filename, array $options = [])
 * @method $this addFromPhpCodeString(string $string, array $options = [])
 * @method static $this fromPoFile(string $filename, array $options = [])
 * @method static $this fromPoString(string $string, array $options = [])
 * @method $this addFromPoFile(string $filename, array $options = [])
 * @method $this addFromPoString(string $string, array $options = [])
 * @method bool toPoFile(string $filename, array $options = [])
 * @method string toPoString(array $options = [])
 * @method static $this fromTwigFile(string $filename, array $options = [])
 * @method static $this fromTwigString(string $string, array $options = [])
 * @method $this addFromTwigFile(string $filename, array $options = [])
 * @method $this addFromTwigString(string $string, array $options = [])
 * @method static $this fromVueJsFile(string $filename, array $options = [])
 * @method static $this fromVueJsString(string $filename, array $options = [])
 * @method $this addFromVueJsFile(string $filename, array $options = [])
 * @method $this addFromVueJsString(string $filename, array $options = [])
 * @method static $this fromXliffFile(string $filename, array $options = [])
 * @method static $this fromXliffString(string $string, array $options = [])
 * @method $this addFromXliffFile(string $filename, array $options = [])
 * @method $this addFromXliffString(string $string, array $options = [])
 * @method bool toXliffFile(string $filename, array $options = [])
 * @method string toXliffString(array $options = [])
 * @method static $this fromYamlFile(string $filename, array $options = [])
 * @method static $this fromYamlString(string $string, array $options = [])
 * @method $this addFromYamlFile(string $filename, array $options = [])
 * @method $this addFromYamlString(string $string, array $options = [])
 * @method bool toYamlFile(string $filename, array $options = [])
 * @method string toYamlString(array $options = [])
 * @method static $this fromYamlDictionaryFile(string $filename, array $options = [])
 * @method static $this fromYamlDictionaryString(string $string, array $options = [])
 * @method $this addFromYamlDictionaryFile(string $filename, array $options = [])
 * @method $this addFromYamlDictionaryString(string $string, array $options = [])
 * @method bool toYamlDictionaryFile(string $filename, array $options = [])
 * @method string toYamlDictionaryString(array $options = [])
 */
class Translations extends ArrayObject
{
    const HEADER_LANGUAGE = 'Language';
    const HEADER_PLURAL = 'Plural-Forms';
    const HEADER_DOMAIN = 'X-Domain';

    public static $options = [
        'defaultHeaders' => [
            'Project-Id-Version' => '',
            'Report-Msgid-Bugs-To' => '',
            'Last-Translator' => '',
            'Language-Team' => '',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
        ],
        'headersSorting' => false,
        'defaultDateHeaders' => [
            'POT-Creation-Date',
            'PO-Revision-Date',
        ],
    ];

    protected $headers;

    protected $translationClass;

    /**
     * @see ArrayObject::__construct()
     */
    public function __construct(
        $input = [],
        $flags = 0,
        $iterator_class = 'ArrayIterator',
        $translationClass = 'Gettext\Translation'
    ) {
        $this->headers = static::$options['defaultHeaders'];

        foreach (static::$options['defaultDateHeaders'] as $header) {
            $this->headers[$header] = date('c');
        }

        $this->headers[self::HEADER_LANGUAGE] = '';

        $this->translationClass = $translationClass;

        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * Magic method to create new instances using extractors
     * For example: Translations::fromMoFile($filename, $options);.
     *
     * @return Translations
     */
    public static function __callStatic($name, $arguments)
    {
        if (!preg_match('/^from(\w+)(File|String)$/i', $name, $matches)) {
            throw new BadMethodCallException("The method $name does not exists");
        }

        return call_user_func_array([new static(), 'add'.ucfirst($name)], $arguments);
    }

    /**
     * Magic method to import/export the translations to a specific format
     * For example: $translations->toMoFile($filename, $options);
     * For example: $translations->addFromMoFile($filename, $options);.
     *
     * @return self|bool
     */
    public function __call($name, $arguments)
    {
        if (!preg_match('/^(addFrom|to)(\w+)(File|String)$/i', $name, $matches)) {
            throw new BadMethodCallException("The method $name does not exists");
        }

        if ($matches[1] === 'addFrom') {
            $extractor = 'Gettext\\Extractors\\'.$matches[2].'::from'.$matches[3];
            $source = array_shift($arguments);
            $options = array_shift($arguments) ?: [];

            call_user_func($extractor, $source, $this, $options);

            return $this;
        }

        $generator = 'Gettext\\Generators\\'.$matches[2].'::to'.$matches[3];

        array_unshift($arguments, $this);

        return call_user_func_array($generator, $arguments);
    }

    /**
     * Magic method to clone each translation on clone the translations object.
     */
    public function __clone()
    {
        $array = [];

        foreach ($this as $key => $translation) {
            $array[$key] = clone $translation;
        }

        $this->exchangeArray($array);
    }

    /**
     * Control the new translations added.
     *
     * @param mixed       $index
     * @param Translation $value
     *
     * @throws InvalidArgumentException If the value is not an instance of Gettext\Translation
     *
     * @return Translation
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($index, $value)
    {
        if (!($value instanceof Translation)) {
            throw new InvalidArgumentException(
                'Only instances of Gettext\\Translation must be added to a Gettext\\Translations'
            );
        }

        $id = $value->getId();

        if ($this->offsetExists($id)) {
            $this[$id]->mergeWith($value);

            return $this[$id];
        }

        parent::offsetSet($id, $value);

        return $value;
    }

    /**
     * Set the plural definition.
     *
     * @param int    $count
     * @param string $rule
     *
     * @return self
     */
    public function setPluralForms($count, $rule)
    {
        if (preg_match('/[a-z]/i', str_replace('n', '', $rule))) {
            throw new \InvalidArgumentException('Invalid Plural form: ' . $rule);
        }
        $this->setHeader(self::HEADER_PLURAL, "nplurals={$count}; plural={$rule};");

        return $this;
    }

    /**
     * Returns the parsed plural definition.
     *
     * @param null|array [count, rule]
     */
    public function getPluralForms()
    {
        $header = $this->getHeader(self::HEADER_PLURAL);

        if (!empty($header)
            && preg_match('/^nplurals\s*=\s*(\d+)\s*;\s*plural\s*=\s*([^;]+)\s*;$/', $header, $matches)
        ) {
            return [intval($matches[1]), $matches[2]];
        }
    }

    /**
     * Set a new header.
     *
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function setHeader($name, $value)
    {
        $name = trim($name);
        $this->headers[$name] = trim($value);

        return $this;
    }

    /**
     * Returns a header value.
     *
     * @param string $name
     *
     * @return null|string
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * Returns all header for this translations (in alphabetic order).
     *
     * @return array
     */
    public function getHeaders()
    {
        if (static::$options['headersSorting']) {
            ksort($this->headers);
        }

        return $this->headers;
    }

    /**
     * Removes all headers.
     *
     * @return self
     */
    public function deleteHeaders()
    {
        $this->headers = [];

        return $this;
    }

    /**
     * Removes one header.
     *
     * @param string $name
     *
     * @return self
     */
    public function deleteHeader($name)
    {
        unset($this->headers[$name]);

        return $this;
    }

    /**
     * Returns the language value.
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->getHeader(self::HEADER_LANGUAGE);
    }

    /**
     * Sets the language and the plural forms.
     *
     * @param string $language
     *
     * @throws InvalidArgumentException if the language hasn't been recognized
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->setHeader(self::HEADER_LANGUAGE, trim($language));

        if (($info = Language::getById($language))) {
            return $this->setPluralForms(count($info->categories), $info->formula);
        }

        throw new InvalidArgumentException(sprintf('The language "%s" is not valid', $language));
    }

    /**
     * Checks whether the language is empty or not.
     *
     * @return bool
     */
    public function hasLanguage()
    {
        $language = $this->getLanguage();

        return (is_string($language) && ($language !== '')) ? true : false;
    }

    /**
     * Set a new domain for this translations.
     *
     * @param string $domain
     *
     * @return self
     */
    public function setDomain($domain)
    {
        $this->setHeader(self::HEADER_DOMAIN, trim($domain));

        return $this;
    }

    /**
     * Returns the domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->getHeader(self::HEADER_DOMAIN);
    }

    /**
     * Checks whether the domain is empty or not.
     *
     * @return bool
     */
    public function hasDomain()
    {
        $domain = $this->getDomain();

        return (is_string($domain) && ($domain !== '')) ? true : false;
    }

    /**
     * Search for a specific translation.
     *
     * @param string|Translation $context  The context of the translation or a translation instance
     * @param string             $original The original string
     * @warning Translations with custom identifiers (e.g. XLIFF unit IDs) cannot be found using this function.
     *
     * @return Translation|false
     */
    public function find($context, $original = '')
    {
        if ($context instanceof Translation) {
            $id = $context->getId();
        } else {
            $id = Translation::generateId($context, $original);
        }

        return $this->offsetExists($id) ? $this[$id] : false;
    }

    /**
     * Count all elements translated
     *
     * @return integer
     */
    public function countTranslated()
    {
        $c = 0;
        foreach ($this as $v) {
            if ($v->hasTranslation()) {
                $c++;
            }
        }
        return $c;
    }

    /**
     * Creates and insert/merges a new translation.
     *
     * @param string $context  The translation context
     * @param string $original The translation original string
     * @param string $plural   The translation original plural string
     *
     * @return Translation The translation created
     */
    public function insert($context, $original, $plural = '')
    {
        return $this->offsetSet(null, $this->createNewTranslation($context, $original, $plural));
    }

    /**
     * Merges this translations with other translations.
     *
     * @param Translations $translations The translations instance to merge with
     * @param int          $options
     *
     * @return self
     */
    public function mergeWith(Translations $translations, $options = Merge::DEFAULTS)
    {
        Merge::mergeHeaders($translations, $this, $options);
        Merge::mergeTranslations($translations, $this, $options);

        return $this;
    }

    /**
     * Create a new instance of a Translation object.
     *
     * @param string $context  The context of the translation
     * @param string $original The original string
     * @param string $plural   The original plural string
     * @return Translation New Translation instance
     */
    public function createNewTranslation($context, $original, $plural = '')
    {
        $class = $this->translationClass;
        return $class::create($context, $original, $plural);
    }
}
