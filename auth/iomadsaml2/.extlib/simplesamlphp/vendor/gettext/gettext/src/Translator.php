<?php

namespace Gettext;

use Gettext\Generators\PhpArray;

class Translator extends BaseTranslator implements TranslatorInterface
{
    protected $domain;
    protected $dictionary = [];
    protected $plurals = [];

    /**
     * Loads translation from a Translations instance, a file on an array.
     *
     * @param Translations|string|array $translations
     *
     * @return static
     */
    public function loadTranslations($translations)
    {
        if (is_object($translations) && $translations instanceof Translations) {
            $translations = PhpArray::generate($translations, ['includeHeaders' => false]);
        } elseif (is_string($translations) && is_file($translations)) {
            $translations = include $translations;
        } elseif (!is_array($translations)) {
            throw new \InvalidArgumentException(
                'Invalid Translator: only arrays, files or instance of Translations are allowed'
            );
        }

        $this->addTranslations($translations);

        return $this;
    }

    /**
     * Set the default domain.
     *
     * @param string $domain
     *
     * @return static
     */
    public function defaultDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function gettext($original)
    {
        return $this->dpgettext($this->domain, null, $original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function ngettext($original, $plural, $value)
    {
        return $this->dnpgettext($this->domain, null, $original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dngettext($domain, $original, $plural, $value)
    {
        return $this->dnpgettext($domain, null, $original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function npgettext($context, $original, $plural, $value)
    {
        return $this->dnpgettext($this->domain, $context, $original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function pgettext($context, $original)
    {
        return $this->dpgettext($this->domain, $context, $original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dgettext($domain, $original)
    {
        return $this->dpgettext($domain, null, $original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dpgettext($domain, $context, $original)
    {
        $translation = $this->getTranslation($domain, $context, $original);

        if (isset($translation[0]) && $translation[0] !== '') {
            return $translation[0];
        }

        return $original;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dnpgettext($domain, $context, $original, $plural, $value)
    {
        $translation = $this->getTranslation($domain, $context, $original);
        $key = $this->getPluralIndex($domain, $value, $translation === false);

        if (isset($translation[$key]) && $translation[$key] !== '') {
            return $translation[$key];
        }

        return ($key === 0) ? $original : $plural;
    }

    /**
     * Set new translations to the dictionary.
     *
     * @param array $translations
     */
    protected function addTranslations(array $translations)
    {
        $domain = isset($translations['domain']) ? $translations['domain'] : '';

        //Set the first domain loaded as default domain
        if ($this->domain === null) {
            $this->domain = $domain;
        }

        if (isset($this->dictionary[$domain])) {
            $this->dictionary[$domain] = array_replace_recursive($this->dictionary[$domain], $translations['messages']);

            return;
        }

        if (!empty($translations['plural-forms'])) {
            list($count, $code) = array_map('trim', explode(';', $translations['plural-forms'], 2));

            // extract just the expression turn 'n' into a php variable '$n'.
            // Slap on a return keyword and semicolon at the end.
            $this->plurals[$domain] = [
                'count' => (int) str_replace('nplurals=', '', $count),
                'code' => str_replace('plural=', 'return ', str_replace('n', '$n', $code)).';',
            ];
        }

        $this->dictionary[$domain] = $translations['messages'];
    }

    /**
     * Search and returns a translation.
     *
     * @param string $domain
     * @param string $context
     * @param string $original
     *
     * @return string|false
     */
    protected function getTranslation($domain, $context, $original)
    {
        return isset($this->dictionary[$domain][$context][$original])
             ? $this->dictionary[$domain][$context][$original]
             : false;
    }

    /**
     * Executes the plural decision code given the number to decide which
     * plural version to take.
     *
     * @param string $domain
     * @param string $n
     * @param bool   $fallback set to true to get fallback plural index
     *
     * @return int
     */
    protected function getPluralIndex($domain, $n, $fallback)
    {
        //Not loaded domain or translation, use a fallback
        if (!isset($this->plurals[$domain]) || $fallback === true) {
            return $n == 1 ? 0 : 1;
        }

        if (!isset($this->plurals[$domain]['function'])) {
            $code = static::fixTerseIfs($this->plurals[$domain]['code']);
            $this->plurals[$domain]['function'] = eval("return function (\$n) { $code };");
        }

        if ($this->plurals[$domain]['count'] <= 2) {
            return call_user_func($this->plurals[$domain]['function'], $n) ? 1 : 0;
        }

        return call_user_func($this->plurals[$domain]['function'], $n);
    }

    /**
     * This function will recursively wrap failure states in brackets if they contain a nested terse if.
     *
     * This because PHP can not handle nested terse if's unless they are wrapped in brackets.
     *
     * This code probably only works for the gettext plural decision codes.
     *
     * return ($n==1 ? 0 : $n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2);
     * becomes
     * return ($n==1 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2));
     *
     * @param string $code  the terse if string
     * @param bool   $inner If inner is true we wrap it in brackets
     *
     * @return string A formatted terse If that PHP can work with.
     */
    private static function fixTerseIfs($code, $inner = false)
    {
        /*
         * (?P<expression>[^?]+)   Capture everything up to ? as 'expression'
         * \?                      ?
         * (?P<success>[^:]+)      Capture everything up to : as 'success'
         * :                       :
         * (?P<failure>[^;]+)      Capture everything up to ; as 'failure'
         */
        preg_match('/(?P<expression>[^?]+)\?(?P<success>[^:]+):(?P<failure>[^;]+)/', $code, $matches);

        // If no match was found then no terse if was present
        if (!isset($matches[0])) {
            return $code;
        }

        $expression = $matches['expression'];
        $success = $matches['success'];
        $failure = $matches['failure'];

        // Go look for another terse if in the failure state.
        $failure = static::fixTerseIfs($failure, true);
        $code = $expression.' ? '.$success.' : '.$failure;

        if ($inner) {
            return "($code)";
        }

        // note the semicolon. We need that for executing the code.
        return "$code;";
    }
}
