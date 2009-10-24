<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Currency
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * include needed classes
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';

/**
 * Class for handling currency notations
 *
 * @category  Zend
 * @package   Zend_Currency
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency
{
    // Constants for defining what currency symbol should be displayed
    const NO_SYMBOL     = 1;
    const USE_SYMBOL    = 2;
    const USE_SHORTNAME = 3;
    const USE_NAME      = 4;

    // Constants for defining the position of the currencysign
    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;

    /**
     * Locale for this currency
     *
     * @var string
     */
    private $_locale = null;

    /**
     * Options array
     *
     * The following options are available
     * 'position'  => Position for the currency sign
     * 'script'    => Script for the output
     * 'format'    => Locale for numeric output
     * 'display'   => Currency detail to show
     * 'precision' => Precision for the currency
     * 'name'      => Name for this currency
     * 'currency'  => 3 lettered international abbreviation
     * 'symbol'    => Currency symbol
     *
     * @var array
     * @see Zend_Locale
     */
    protected $_options = array(
        'position'  => self::STANDARD,
        'script'    => null,
        'format'    => null,
        'display'   => self::NO_SYMBOL,
        'precision' => 2,
        'name'      => null,
        'currency'  => null,
        'symbol'    => null
    );

    /**
     * Creates a currency instance. Every supressed parameter is used from the actual or the given locale.
     *
     * @param  string             $currency OPTIONAL currency short name
     * @param  string|Zend_Locale $locale   OPTIONAL locale name
     * @throws Zend_Currency_Exception When currency is invalid
     */
    public function __construct($currency = null, $locale = null)
    {
        if (Zend_Locale::isLocale($currency, true, false)) {
            $temp     = $locale;
            $locale   = $currency;
            $currency = $temp;
        }

        $this->setLocale($locale);

        // Get currency details
        $this->_options['currency'] = self::getShortName($currency, $this->_locale);
        $this->_options['name']     = self::getName($currency, $this->_locale);
        $this->_options['symbol']   = self::getSymbol($currency, $this->_locale);

        if (($this->_options['currency'] === null) and ($this->_options['name'] === null)) {
            require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("Currency '$currency' not found");
        }

        // Get the format
        $this->_options['display']  = self::NO_SYMBOL;
        if (empty($this->_options['symbol']) === false) {
            $this->_options['display'] = self::USE_SYMBOL;
        } else if (empty($this->_options['currency']) === false) {
            $this->_options['display'] = self::USE_SHORTNAME;
        }
    }

    /**
     * Returns a localized currency string
     *
     * @param  integer|float $value   Currency value
     * @param  array         $options OPTIONAL options to set temporary
     * @throws Zend_Currency_Exception When the value is not a number
     * @return string
     */
    public function toCurrency($value, array $options = array())
    {
        // Validate the passed number
        if ((isset($value) === false) or (is_numeric($value) === false)) {
            require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("Value '$value' has to be numeric");
        }

        $options = $this->_checkOptions($options) + $this->_options;

        // Format the number
        $format = $options['format'];
        $locale = $this->_locale;
        if (empty($format)) {
            $format = Zend_Locale_Data::getContent($this->_locale, 'currencynumber');
        } else if (Zend_Locale::isLocale($format, true, false)) {
            $locale = $format;
            $format = Zend_Locale_Data::getContent($format, 'currencynumber');
        }

        $symbols  = Zend_Locale_Data::getList($locale, 'symbols');
        $original = $value;
        $value    = Zend_Locale_Format::toNumber($value, array('locale'        => $locale,
                                                               'number_format' => $format,
                                                              'precision'     => $options['precision']));

        if ($options['position'] !== self::STANDARD) {
            $value = str_replace('¤', '', $value);
            $space = '';
            if (iconv_strpos($value, ' ') !== false) {
                $value = str_replace(' ', '', $value);
                $space = ' ';
            }

            if ($options['position'] == self::LEFT) {
                $value = '¤' . $space . $value;
            } else {
                $value = $value . $space . '¤';
            }
        }

        // Localize the number digits
        if (empty($options['script']) === false) {
            $value = Zend_Locale_Format::convertNumerals($value, 'Latn', $options['script']);
        }

        // Get the sign to be placed next to the number
        if (is_numeric($options['display']) === false) {
            $sign = $options['display'];
        } else {
            switch($options['display']) {
                case self::USE_SYMBOL:
                    $sign = $this->_extractPattern($options['symbol'], $original);
                    break;

                case self::USE_SHORTNAME:
                    $sign = $options['currency'];
                    break;

                case self::USE_NAME:
                    $sign = $options['name'];
                    break;

                default:
                    $sign = '';
                    $value = str_replace(' ', '', $value);
                    break;
            }
        }

        $value = str_replace('¤', $sign, $value);
        return $value;
    }

    /**
     * Internal method to extract the currency pattern
     * when a choice is given based on the given value
     *
     * @param  string $pattern
     * @param  float|integer $value
     * @return string
     */
    private function _extractPattern($pattern, $value)
    {
        if (strpos($pattern, '|') === false) {
            return $pattern;
        }

        $patterns = explode('|', $pattern);
        $token    = $pattern;
        $value    = trim(str_replace('¤', '', $value));
        krsort($patterns);
        foreach($patterns as $content) {
            if (strpos($content, '<') !== false) {
                $check = iconv_substr($content, 0, iconv_strpos($content, '<'));
                $token = iconv_substr($content, iconv_strpos($content, '<') + 1);
                if ($check < $value) {
                    return $token;
                }
            } else {
                $check = iconv_substr($content, 0, iconv_strpos($content, '≤'));
                $token = iconv_substr($content, iconv_strpos($content, '≤') + 1);
                if ($check <= $value) {
                    return $token;
                }
            }

        }

        return $token;
    }

    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  array $options (Optional) Options to set
     * @return Zend_Currency
     */
    public function setFormat(array $options = array())
    {
        $this->_options = $this->_checkOptions($options) + $this->_options;
        return $this;
    }

    /**
     * Internal function for checking static given locale parameter
     *
     * @param  string             $currency (Optional) Currency name
     * @param  string|Zend_Locale $locale   (Optional) Locale to display informations
     * @throws Zend_Currency_Exception When locale contains no region
     * @return string The extracted locale representation as string
     */
    private function _checkParams($currency = null, $locale = null)
    {
        // Manage the params
        if ((empty($locale)) and (!empty($currency)) and
            (Zend_Locale::isLocale($currency, true, false))) {
            $locale   = $currency;
            $currency = null;
        }

        // Validate the locale and get the country short name
        $country = null;
        if ((Zend_Locale::isLocale($locale, true, false)) and (strlen($locale) > 4)) {
            $country = substr($locale, (strpos($locale, '_') + 1));
        } else {
            require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception("No region found within the locale '" . (string) $locale . "'");
        }

        // Get the available currencies for this country
        $data = Zend_Locale_Data::getContent($locale, 'currencytoregion', $country);
        if ((empty($currency) === false) and (empty($data) === false)) {
            $abbreviation = $currency;
        } else {
            $abbreviation = $data;
        }

        return array('locale' => $locale, 'currency' => $currency, 'name' => $abbreviation, 'country' => $country);
    }

    /**
     * Returns the actual or details of other currency symbols,
     * when no symbol is available it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string             $currency (Optional) Currency name
     * @param  string|Zend_Locale $locale   (Optional) Locale to display informations
     * @return string
     */
    public function getSymbol($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['symbol'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the symbol
        $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbol', $params['currency']);
        if (empty($symbol) === true) {
            $symbol = Zend_Locale_Data::getContent($params['locale'], 'currencysymbol', $params['name']);
        }

        if (empty($symbol) === true) {
            return null;
        }

        return $symbol;
    }

    /**
     * Returns the actual or details of other currency shortnames
     *
     * @param  string             $currency OPTIONAL Currency's name
     * @param  string|Zend_Locale $locale   OPTIONAL The locale
     * @return string
     */
    public function getShortName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['currency'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the shortname
        if (empty($params['currency']) === true) {
            return $params['name'];
        }

        $list = Zend_Locale_Data::getContent($params['locale'], 'currencytoname', $params['currency']);
        if (empty($list) === true) {
            $list = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['currency']);
            if (empty($list) === false) {
                $list = $params['currency'];
            }
        }

        if (empty($list) === true) {
            return null;
        }

        return $list;
    }

    /**
     * Returns the actual or details of other currency names
     *
     * @param  string             $currency (Optional) Currency's short name
     * @param  string|Zend_Locale $locale   (Optional) The locale
     * @return string
     */
    public function getName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['name'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the name
        $name = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['currency']);
        if (empty($name) === true) {
            $name = Zend_Locale_Data::getContent($params['locale'], 'nametocurrency', $params['name']);
        }

        if (empty($name) === true) {
            return null;
        }

        return $name;
    }

    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string $currency OPTIONAL Currency's short name
     * @throws Zend_Currency_Exception When no currency was defined
     * @return array List of regions
     */
    public function getRegionList($currency = null)
    {
        if ($currency === null) {
            $currency = $this->_options['currency'];
        }

        if (empty($currency) === true) {
            require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception('No currency defined');
        }

        $data = Zend_Locale_Data::getContent('', 'regiontocurrency', $currency);

        $result = explode(' ', $data);
        return $result;
    }

    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     * If no region is given, the actual region is used
     *
     * @param  string $region OPTIONAL Region to return the currencies for
     * @return array List of currencies
     */
    public function getCurrencyList($region = null)
    {
        if (empty($region) === true) {
            if (strlen($this->_locale) > 4) {
                $region = substr($this->_locale, (strpos($this->_locale, '_') + 1));
            }
        }

        return Zend_Locale_Data::getList('', 'regiontocurrency', $region);
    }

    /**
     * Returns the actual currency name
     *
     * @return string
     */
    public function toString()
    {
        return (empty($this->_options['name']) === false) ? $this->_options['name'] : $this->_options['currency'];
    }

    /**
     * Returns the currency name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the set cache
     *
     * @return Zend_Cache_Core The set cache
     */
    public static function getCache()
    {
        $cache = Zend_Locale_Data::getCache();
        return $cache;
    }

    /**
     * Sets a cache for Zend_Currency
     *
     * @param  Zend_Cache_Core $cache Cache to set
     * @return void
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        Zend_Locale_Data::setCache($cache);
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        return Zend_Locale_Data::hasCache();
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        Zend_Locale_Data::removeCache();
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache()
    {
        Zend_Locale_Data::clearCache();
    }

    /**
     * Sets a new locale for data retreivement
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for parsing input
     * @throws Zend_Currency_Exception When the given locale does not exist
     * @return Zend_Currency Provides fluent interface
     */
    public function setLocale($locale = null)
    {
        require_once 'Zend/Locale.php';
        try {
            $this->_locale = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Currency/Exception.php';
            throw new Zend_Currency_Exception($e->getMessage());
        }

        // Get currency details
        $this->_options['currency'] = $this->getShortName(null, $this->_locale);
        $this->_options['name']     = $this->getName(null, $this->_locale);
        $this->_options['symbol']   = $this->getSymbol(null, $this->_locale);

        return $this;
    }

    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Internal method for checking the options array
     *
     * @param  array $options Options to check
     * @throws Zend_Currency_Exception On unknown position
     * @throws Zend_Currency_Exception On unknown locale
     * @throws Zend_Currency_Exception On unknown display
     * @throws Zend_Currency_Exception On precision not between -1 and 30
     * @throws Zend_Currency_Exception On problem with script conversion
     * @throws Zend_Currency_Exception On unknown options
     * @return array
     */
    private function _checkOptions(array $options = array())
    {
        if (count($options) === 0) {
            return $this->_options;
        }

        foreach ($options as $name => $value) {
            $name = strtolower($name);
            if ($name !== 'format') {
                if (gettype($value) === 'string') {
                    $value = strtolower($value);
                }
            }

            switch($name) {
                case 'position':
                    if (($value !== self::STANDARD) and ($value !== self::RIGHT) and ($value !== self::LEFT)) {
                        require_once 'Zend/Currency/Exception.php';
                        throw new Zend_Currency_Exception("Unknown position '" . $value . "'");
                    }

                    break;

                case 'format':
                    if ((empty($value) === false) and (Zend_Locale::isLocale($value, null, false) === false)) {
                        require_once 'Zend/Currency/Exception.php';
                        throw new Zend_Currency_Exception("'" .
                            ((gettype($value) === 'object') ? get_class($value) : $value)
                            . "' is not a known locale.");
                    }
                    break;

                case 'display':
                    if (is_numeric($value) and ($value !== self::NO_SYMBOL) and ($value !== self::USE_SYMBOL) and
                        ($value !== self::USE_SHORTNAME) and ($value !== self::USE_NAME)) {
                        require_once 'Zend/Currency/Exception.php';
                        throw new Zend_Currency_Exception("Unknown display '$value'");
                    }
                    break;

                case 'precision':
                    if ($value === null) {
                        $value = -1;
                    }

                    if (($value < -1) or ($value > 30)) {
                        require_once 'Zend/Currency/Exception.php';
                        throw new Zend_Currency_Exception("'$value' precision has to be between -1 and 30.");
                    }
                    break;

                case 'script':
                    try {
                        Zend_Locale_Format::convertNumerals(0, $options['script']);
                    } catch (Zend_Locale_Exception $e) {
                        require_once 'Zend/Currency/Exception.php';
                        throw new Zend_Currency_Exception($e->getMessage());
                    }
                    break;

                case 'name':
                    // Break intentionally omitted
                case 'currency':
                    // Break intentionally omitted
                case 'symbol':
                    // Unchecked options
                    break;

                default:
                    require_once 'Zend/Currency/Exception.php';
                    throw new Zend_Currency_Exception("Unknown option: '$name' = '$value'");
                    break;
            }
        }

        return $options;
    }
}
