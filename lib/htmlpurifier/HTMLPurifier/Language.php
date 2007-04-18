<?php

require_once 'HTMLPurifier/LanguageFactory.php';

class HTMLPurifier_Language
{
    
    /**
     * ISO 639 language code of language. Prefers shortest possible version
     */
    var $code = 'en';
    
    /**
     * Fallback language code
     */
    var $fallback = false;
    
    /**
     * Array of localizable messages
     */
    var $messages = array();
    
    /**
     * Has the language object been loaded yet?
     * @private
     */
    var $_loaded = false;
    
    /**
     * Loads language object with necessary info from factory cache
     * @note This is a lazy loader
     */
    function load() {
        if ($this->_loaded) return;
        $factory = HTMLPurifier_LanguageFactory::instance();
        $factory->loadLanguage($this->code);
        foreach ($factory->keys as $key) {
            $this->$key = $factory->cache[$this->code][$key];
        }
        $this->_loaded = true;
    }
    
    /**
     * Retrieves a localised message. Does not perform any operations.
     * @param $key string identifier of message
     * @return string localised message
     */
    function getMessage($key) {
        if (!$this->_loaded) $this->load();
        if (!isset($this->messages[$key])) return '';
        return $this->messages[$key];
    }
    
}

?>