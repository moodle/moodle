<?php

namespace Gettext;

class GettextTranslator extends BaseTranslator implements TranslatorInterface
{
    /**
     * Constructor. Detects the current language using the environment variables.
     *
     * @param string $language
     */
    public function __construct($language = null)
    {
        if (!function_exists('gettext')) {
            throw new \RuntimeException('This class require the gettext extension for PHP');
        }

        //detects the language environment respecting the priority order
        //http://php.net/manual/en/function.gettext.php#114062
        if (empty($language)) {
            $language = getenv('LANGUAGE') ?: getenv('LC_ALL') ?: getenv('LC_MESSAGES') ?: getenv('LANG');
        }

        if (!empty($language)) {
            $this->setLanguage($language);
        }
    }

    /**
     * Define the current locale.
     *
     * @param string   $language
     * @param int|null $category
     *
     * @return self
     */
    public function setLanguage($language, $category = null)
    {
        if ($category === null) {
            $category = defined('LC_MESSAGES') ? LC_MESSAGES : LC_ALL;
        }

        setlocale($category, $language);
        putenv('LANGUAGE='.$language);

        return $this;
    }

    /**
     * Loads a gettext domain.
     *
     * @param string $domain
     * @param string $path
     * @param bool   $default
     *
     * @return self
     */
    public function loadDomain($domain, $path = null, $default = true)
    {
        bindtextdomain($domain, $path);
        bind_textdomain_codeset($domain, 'UTF-8');

        if ($default) {
            textdomain($domain);
        }

        return $this;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function gettext($original)
    {
        return gettext($original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function ngettext($original, $plural, $value)
    {
        return ngettext($original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dngettext($domain, $original, $plural, $value)
    {
        return dngettext($domain, $original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function npgettext($context, $original, $plural, $value)
    {
        $message = $context."\x04".$original;
        $translation = ngettext($message, $plural, $value);

        return ($translation === $message) ? $original : $translation;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function pgettext($context, $original)
    {
        $message = $context."\x04".$original;
        $translation = gettext($message);

        return ($translation === $message) ? $original : $translation;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dgettext($domain, $original)
    {
        return dgettext($domain, $original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dpgettext($domain, $context, $original)
    {
        $message = $context."\x04".$original;
        $translation = dgettext($domain, $message);

        return ($translation === $message) ? $original : $translation;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dnpgettext($domain, $context, $original, $plural, $value)
    {
        $message = $context."\x04".$original;
        $translation = dngettext($domain, $message, $plural, $value);

        return ($translation === $message) ? $original : $translation;
    }
}
