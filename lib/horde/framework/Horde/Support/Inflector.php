<?php
/**
 * Copyright 2007-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */

/**
 * Horde Inflector class.
 *
 * @todo Add the locale-bubbling pattern from
 *       Horde_Date_Parser/Horde_Support_Numerizer
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_Inflector
{
    /**
     * Inflection cache
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * Rules for pluralizing English nouns.
     *
     * @var array
     */
    protected $_pluralizationRules = array(
        '/move$/i' => 'moves',
        '/sex$/i' => 'sexes',
        '/child$/i' => 'children',
        '/man$/i' => 'men',
        '/foot$/i' => 'feet',
        '/person$/i' => 'people',
        '/(quiz)$/i' => '$1zes',
        '/^(ox)$/i' => '$1en',
        '/(m|l)ouse$/i' => '$1ice',
        '/(matr|vert|ind)ix|ex$/i' => '$1ices',
        '/(x|ch|ss|sh)$/i' => '$1es',
        '/([^aeiouy]|qu)ies$/i' => '$1y',
        '/([^aeiouy]|qu)y$/i' => '$1ies',
        '/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '$1a',
        '/(buffal|tomat)o$/i' => '$1oes',
        '/(bu)s$/i' => '$1ses',
        '/(alias|status)$/i' => '$1es',
        '/(octop|vir)us$/i' => '$1i',
        '/(ax|test)is$/i' => '$1es',
        '/s$/i' => 's',
        '/$/' => 's',
    );

    /**
     * Rules for singularizing English nouns.
     *
     * @var array
     */
    protected $_singularizationRules = array(
        '/cookies$/i' => 'cookie',
        '/moves$/i' => 'move',
        '/sexes$/i' => 'sex',
        '/children$/i' => 'child',
        '/men$/i' => 'man',
        '/feet$/i' => 'foot',
        '/people$/i' => 'person',
        '/databases$/i'=> 'database',
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/(.*)s$/i' => '\1',
    );

    /**
     * An array of words with the same singular and plural spellings.
     *
     * @var array
     */
    protected $_uncountables = array(
        'aircraft',
        'cannon',
        'deer',
        'equipment',
        'fish',
        'information',
        'money',
        'moose',
        'rice',
        'series',
        'sheep',
        'species',
        'swine',
    );

    /**
     * Constructor.
     *
     * Stores a map of the uncountable words for quicker checks.
     */
    public function __construct()
    {
        $this->_uncountables_keys = array_flip($this->_uncountables);
    }

    /**
     * Adds an uncountable word.
     *
     * @param string $word The uncountable word.
     */
    public function uncountable($word)
    {
        $this->_uncountables[] = $word;
        $this->_uncountables_keys[$word] = true;
    }

    /**
     * Singular English word to pluralize.
     *
     * @param string $word Word to pluralize.
     *
     * @return string Plural form of $word.
     */
    public function pluralize($word)
    {
        if ($plural = $this->getCache($word, 'pluralize')) {
            return $plural;
        }

        if (isset($this->_uncountables_keys[$word])) {
            return $word;
        }

        foreach ($this->_pluralizationRules as $regexp => $replacement) {
            $plural = preg_replace($regexp, $replacement, $word, -1, $matches);
            if ($matches > 0) {
                return $this->setCache($word, 'pluralize', $plural);
            }
        }

        return $this->setCache($word, 'pluralize', $word);
    }

    /**
     * Plural English word to singularize.
     *
     * @param string $word Word to singularize.
     *
     * @return string Singular form of $word.
     */
    public function singularize($word)
    {
        if ($singular = $this->getCache($word, 'singularize')) {
            return $singular;
        }

        if (isset($this->_uncountables_keys[$word])) {
            return $word;
        }

        foreach ($this->_singularizationRules as $regexp => $replacement) {
            $singular = preg_replace($regexp, $replacement, $word, -1, $matches);
            if ($matches > 0) {
                return $this->setCache($word, 'singularize', $singular);
            }
        }

        return $this->setCache($word, 'singularize', $word);
    }

    /**
     * Camel-cases a word.
     *
     * @todo Do we want locale-specific or locale-independent camel casing?
     *
     * @param string $word         The word to camel-case.
     * @param string $firstLetter  Whether to upper or lower case the first.
     *                             letter of each slash-separated section.
     *
     * @return string Camelized $word
     */
    public function camelize($word, $firstLetter = 'upper')
    {
        if ($camelized = $this->getCache($word, 'camelize' . $firstLetter)) {
            return $camelized;
        }

        $camelized = $word;
        if (Horde_String::lower($camelized) != $camelized &&
            strpos($camelized, '_') !== false) {
            $camelized = str_replace('_', '/', $camelized);
        }
        if (strpos($camelized, '/') !== false) {
            $camelized = str_replace('/', '/ ', $camelized);
        }
        if (strpos($camelized, '_') !== false) {
            $camelized = strtr($camelized, '_', ' ');
        }

        $camelized = str_replace(' ', '', Horde_String::ucwords($camelized));

        if ($firstLetter == 'lower') {
            $parts = array();
            foreach (explode('/', $camelized) as $part) {
                $part[0] = Horde_String::lower($part[0]);
                $parts[] = $part;
            }
            $camelized = implode('/', $parts);
        }

        return $this->setCache($word, 'camelize' . $firstLetter, $camelized);
    }

    /**
     * Capitalizes all the words and replaces some characters in the string to
     * create a nicer looking title.
     *
     * Titleize is meant for creating pretty output.
     *
     * See:
     * - http://daringfireball.net/2008/05/title_case
     * - http://daringfireball.net/2008/08/title_case_update
     *
     * Examples:
     * 1. titleize("man from the boondocks") => "Man From The Boondocks"
     * 2. titleize("x-men: the last stand")  => "X Men: The Last Stand"
     */
    public function titleize($word)
    {
        throw new Exception('not implemented yet');
    }

    /**
     * The reverse of camelize().
     *
     * Makes an underscored form from the expression in the string.
     *
     * Examples:
     * 1. underscore("ActiveRecord")        => "active_record"
     * 2. underscore("ActiveRecord_Errors") => "active_record_errors"
     *
     * @todo Do we want locale-specific or locale-independent lowercasing?
     */
    public function underscore($camelCasedWord)
    {
        $word = $camelCasedWord;
        if ($result = $this->getCache($word, 'underscore')) {
            return $result;
        }
        $result = Horde_String::lower(preg_replace('/([a-z])([A-Z])/', "\${1}_\${2}", $word));
        return $this->setCache($word, 'underscore', $result);
    }

    /**
     * Replaces underscores with dashes in the string.
     *
     * Example:
     * 1. dasherize("puni_puni") => "puni-puni"
     */
    public function dasherize($underscoredWord)
    {
        if ($result = $this->getCache($underscoredWord, 'dasherize')) {
            return $result;
        }

        $result = str_replace('_', '-', $this->underscore($underscoredWord));
        return $this->setCache($underscoredWord, 'dasherize', $result);
    }

    /**
     * Capitalizes the first word and turns underscores into spaces and strips
     * _id.
     *
     * Like titleize(), this is meant for creating pretty output.
     *
     * Examples:
     * 1. humanize("employee_salary") => "Employee salary"
     * 2. humanize("author_id")       => "Author"
     */
    public function humanize($lowerCaseAndUnderscoredWord)
    {
        $word = $lowerCaseAndUnderscoredWord;
        if ($result = $this->getCache($word, 'humanize')) {
            return $result;
        }

        $result = ucfirst(str_replace('_', ' ', $this->underscore($word)));
        if (substr($result, -3, 3) == ' id') {
            $result = str_replace(' id', '', $result);
        }
        return $this->setCache($word, 'humanize', $result);
    }

    /**
     * Removes the module part from the expression in the string.
     *
     * Examples:
     * 1. demodulize("Fax_Job") => "Job"
     * 1. demodulize("User")    => "User"
     */
    public function demodulize($classNameInModule)
    {
        $result = explode('_', $classNameInModule);
        return array_pop($result);
    }

    /**
     * Creates the name of a table like Rails does for models to table names.
     *
     * This method uses the pluralize() method on the last word in the string.
     *
     * Examples:
     * 1. tableize("RawScaledScorer") => "raw_scaled_scorers"
     * 2. tableize("egg_and_ham")     => "egg_and_hams"
     * 3. tableize("fancyCategory")   => "fancy_categories"
     */
    public function tableize($className)
    {
        if ($result = $this->getCache($className, 'tableize')) {
            return $result;
        }

        $result = $this->pluralize($this->underscore($className));
        $result = str_replace('/', '_', $result);
        return $this->setCache($className, 'tableize', $result);
    }

    /**
     * Creates a class name from a table name like Rails does for table names
     * to models.
     *
     * Examples:
     * 1. classify("egg_and_hams") => "EggAndHam"
     * 2. classify("post")         => "Post"
     */
    public function classify($tableName)
    {
        if ($result = $this->getCache($tableName, 'classify')) {
            return $result;
        }
        $result = $this->camelize($this->singularize($tableName));

        // classes use underscores instead of slashes for namespaces
        $result = str_replace('/', '_', $result);
        return $this->setCache($tableName, 'classify', $result);
    }

    /**
     * Creates a foreign key name from a class name.
     *
     * $separateClassNameAndIdWithUnderscore sets whether the method should put
     * '_' between the name and 'id'.
     *
     * Examples:
     * 1. foreignKey("Message")        => "message_id"
     * 2. foreignKey("Message", false) => "messageid"
     * 3. foreignKey("Fax_Job")        => "fax_job_id"
     */
    public function foreignKey($className, $separateClassNameAndIdWithUnderscore = true)
    {
        throw new Exception('not implemented yet');
    }

    /**
     * Turns a number into an ordinal string used to denote the position in an
     * ordered sequence such as 1st, 2nd, 3rd, 4th.
     *
     * Examples:
     * 1. ordinalize(1)      => "1st"
     * 2. ordinalize(2)      => "2nd"
     * 3. ordinalize(1002)   => "1002nd"
     * 4. ordinalize(1003)   => "1003rd"
     */
    public function ordinalize($number)
    {
        throw new Exception('not implemented yet');
    }

    /**
     * Clears the inflection cache.
     */
    public function clearCache()
    {
        $this->_cache = array();
    }

    /**
     * Retuns a cached inflection.
     *
     * @return string | false
     */
    public function getCache($word, $rule)
    {
        return isset($this->_cache[$word . '|' . $rule]) ?
            $this->_cache[$word . '|' . $rule] : false;
    }

    /**
     * Caches an inflection.
     *
     * @param string $word   The word being inflected.
     * @param string $rule   The inflection rule.
     * @param string $value  The inflected value of $word.
     *
     * @return string The inflected value
     */
    public function setCache($word, $rule, $value)
    {
        $this->_cache[$word . '|' . $rule] = $value;
        return $value;
    }
}
