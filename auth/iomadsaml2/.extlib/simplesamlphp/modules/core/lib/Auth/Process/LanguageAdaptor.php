<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Locale\Language;
use SimpleSAML\Logger;

/**
 * Filter to set and get language settings from attributes.
 *
 * @author Andreas Åkre Solberg, UNINETT AS.
 * @package SimpleSAMLphp
 */
class LanguageAdaptor extends \SimpleSAML\Auth\ProcessingFilter
{
    /** @var string */
    private $langattr = 'preferredLanguage';


    /**
     * Initialize this filter.
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));

        if (array_key_exists('attributename', $config)) {
            $this->langattr = $config['attributename'];
        }
    }


    /**
     * Apply filter to add or replace attributes.
     *
     * Add or replace existing attributes with the configured values.
     *
     * @param array &$request  The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        $attributes = &$request['Attributes'];

        $attrlang = null;
        if (array_key_exists($this->langattr, $attributes)) {
            $attrlang = $attributes[$this->langattr][0];
        }

        $lang = Language::getLanguageCookie();

        if (isset($attrlang)) {
            Logger::debug('LanguageAdaptor: Language in attribute was set [' . $attrlang . ']');
        }
        if (isset($lang)) {
            Logger::debug('LanguageAdaptor: Language in session was set [' . $lang . ']');
        }

        if (isset($attrlang) && !isset($lang)) {
            // Language set in attribute but not in cookie - update cookie
            Language::setLanguageCookie($attrlang);
        } elseif (!isset($attrlang) && isset($lang)) {
            // Language set in cookie, but not in attribute. Update attribute
            $request['Attributes'][$this->langattr] = [$lang];
        }
    }
}
