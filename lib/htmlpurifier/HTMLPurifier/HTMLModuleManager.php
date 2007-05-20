<?php

require_once 'HTMLPurifier/HTMLModule.php';
require_once 'HTMLPurifier/ElementDef.php';

require_once 'HTMLPurifier/ContentSets.php';
require_once 'HTMLPurifier/AttrTypes.php';
require_once 'HTMLPurifier/AttrCollections.php';

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/AttrDef/Enum.php';

// W3C modules
require_once 'HTMLPurifier/HTMLModule/CommonAttributes.php';
require_once 'HTMLPurifier/HTMLModule/Text.php';
require_once 'HTMLPurifier/HTMLModule/Hypertext.php';
require_once 'HTMLPurifier/HTMLModule/List.php';
require_once 'HTMLPurifier/HTMLModule/Presentation.php';
require_once 'HTMLPurifier/HTMLModule/Edit.php';
require_once 'HTMLPurifier/HTMLModule/Bdo.php';
require_once 'HTMLPurifier/HTMLModule/Tables.php';
require_once 'HTMLPurifier/HTMLModule/Image.php';
require_once 'HTMLPurifier/HTMLModule/StyleAttribute.php';
require_once 'HTMLPurifier/HTMLModule/Legacy.php';
require_once 'HTMLPurifier/HTMLModule/Target.php';

// proprietary modules
require_once 'HTMLPurifier/HTMLModule/TransformToStrict.php';
require_once 'HTMLPurifier/HTMLModule/TransformToXHTML11.php';

HTMLPurifier_ConfigSchema::define(
    'HTML', 'Doctype', null, 'string/null',
    'Doctype to use, valid values are HTML 4.01 Transitional, HTML 4.01 '.
    'Strict, XHTML 1.0 Transitional, XHTML 1.0 Strict, XHTML 1.1. '.
    'Technically speaking this is not actually a doctype (as it does '.
    'not identify a corresponding DTD), but we are using this name '.
    'for sake of simplicity. This will override any older directives '.
    'like %Core.XHTML or %HTML.Strict.'
);

class HTMLPurifier_HTMLModuleManager
{
    
    /**
     * Array of HTMLPurifier_Module instances, indexed by module's class name.
     * All known modules, regardless of use, are in this array.
     */
    var $modules = array();
    
    /**
     * String doctype we will validate against. See $validModules for use.
     * 
     * @note
     * There is a special doctype '*' that acts both as the "default"
     * doctype if a customized system only defines one doctype and
     * also a catch-all doctype that gets merged into all the other
     * module collections. When possible, use a private collection to
     * share modules between doctypes: this special doctype is to
     * make life more convenient for users.
     */
    var $doctype;
    var $doctypeAliases = array(); /**< Lookup array of strings to real doctypes */
    
    /**
     * Associative array: $collections[$type][$doctype] = list of modules.
     * This is used to logically separate types of functionality so that
     * based on the doctype and other configuration settings they may
     * be easily switched and on and off. Custom setups may not need
     * to use this abstraction, opting to have only one big collection
     * with one valid doctype.
     */
    var $collections = array();
    
    /**
     * Modules that may be used in a valid doctype of this kind.
     * Correctional and leniency modules should not be placed in this
     * array unless the user said so: don't stuff every possible lenient
     * module for this doctype in here.
     */
    var $validModules = array();
    var $validCollections = array(); /**< Collections to merge into $validModules */
    
    /**
     * Modules that we will allow in input, subset of $validModules. Single
     * element definitions may result in us consulting validModules.
     */
    var $activeModules = array();
    var $activeCollections = array(); /**< Collections to merge into $activeModules */
    
    var $counter = 0; /**< Designates next available integer order for modules. */
    var $initialized = false; /**< Says whether initialize() was called */
    
    /**
     * Specifies what doctype to siphon new modules from addModule() to,
     * or false to disable the functionality. Must be used in conjunction
     * with $autoCollection.
     */
    var $autoDoctype = false;
    /**
     * Specifies what collection to siphon new modules from addModule() to,
     * or false to disable the functionality. Must be used in conjunction
     * with $autoCollection.
     */
    var $autoCollection = false;
    
    /** Associative array of element name to defining modules (always array) */
    var $elementLookup = array();
    
    /** List of prefixes we should use for resolving small names */
    var $prefixes = array('HTMLPurifier_HTMLModule_');
    
    var $contentSets; /**< Instance of HTMLPurifier_ContentSets */
    var $attrTypes; /**< Instance of HTMLPurifier_AttrTypes */
    var $attrCollections; /**< Instance of HTMLPurifier_AttrCollections */
    
    /**
     * @param $blank If true, don't do any initializing
     */
    function HTMLPurifier_HTMLModuleManager($blank = false) {
        
        // the only editable internal object. The rest need to
        // be manipulated through modules
        $this->attrTypes = new HTMLPurifier_AttrTypes();
        
        if (!$blank) $this->initialize();
        
    }
    
    function initialize() {
        $this->initialized = true;
        
        // load default modules to the recognized modules list (not active)
        $modules = array(
            // define
            'CommonAttributes',
            'Text', 'Hypertext', 'List', 'Presentation',
            'Edit', 'Bdo', 'Tables', 'Image', 'StyleAttribute',
            'Target',
            // define-redefine
            'Legacy',
            // redefine
            'TransformToStrict', 'TransformToXHTML11'
        );
        foreach ($modules as $module) {
            $this->addModule($module);
        }
        
        // Safe modules for supported doctypes. These are included
        // in the valid and active module lists by default
        $this->collections['Safe'] = array(
            '_Common' => array( // leading _ indicates private
                'CommonAttributes', 'Text', 'Hypertext', 'List',
                'Presentation', 'Edit', 'Bdo', 'Tables', 'Image',
                'StyleAttribute'
            ),
            // HTML definitions, defer to XHTML definitions
            'HTML 4.01 Transitional' => array(array('XHTML 1.0 Transitional')),
            'HTML 4.01 Strict' => array(array('XHTML 1.0 Strict')),
            // XHTML definitions
            'XHTML 1.0 Transitional' => array( array('XHTML 1.0 Strict'), 'Legacy', 'Target' ),
            'XHTML 1.0 Strict' => array(array('_Common')),
            'XHTML 1.1' => array(array('_Common')),
        );
        
        // Modules that specify elements that are unsafe from untrusted
        // third-parties. These should be registered in $validModules but
        // almost never $activeModules unless you really know what you're
        // doing.
        $this->collections['Unsafe'] = array();
        
        // Modules to import if lenient mode (attempt to convert everything
        // to a valid representation) is on. These must not be in $validModules
        // unless specified so.
        $this->collections['Lenient'] = array(
            'HTML 4.01 Strict' => array(array('XHTML 1.0 Strict')),
            'XHTML 1.0 Strict' => array('TransformToStrict'),
            'XHTML 1.1' => array(array('XHTML 1.0 Strict'), 'TransformToXHTML11')
        );
        
        // Modules to import if correctional mode (correct everything that
        // is feasible to strict mode) is on. These must not be in $validModules
        // unless specified so.
        $this->collections['Correctional'] = array(
            'HTML 4.01 Transitional' => array(array('XHTML 1.0 Transitional')),
            'XHTML 1.0 Transitional' => array('TransformToStrict'), // probably want a different one
        );
        
        // User-space modules, custom code or whatever
        $this->collections['Extension'] = array();
        
        // setup active versus valid modules. ORDER IS IMPORTANT!
        // definition modules
        $this->makeCollectionActive('Safe');
        $this->makeCollectionValid('Unsafe');
        // redefinition modules
        $this->makeCollectionActive('Lenient');
        $this->makeCollectionActive('Correctional');
        
        $this->autoDoctype    = '*';
        $this->autoCollection = 'Extension';
        
    }
    
    /**
     * Adds a module to the recognized module list. This does not
     * do anything else: the module must be added to a corresponding
     * collection to be "activated".
     * @param $module Mixed: string module name, with or without
     *                HTMLPurifier_HTMLModule prefix, or instance of
     *                subclass of HTMLPurifier_HTMLModule.
     * @note This function will not call autoload, you must instantiate
     *       (and thus invoke) autoload outside the method.
     * @note If a string is passed as a module name, different variants
     *       will be tested in this order:
     *          - Check for HTMLPurifier_HTMLModule_$name
     *          - Check all prefixes with $name in order they were added
     *          - Check for literal object name
     *          - Throw fatal error
     *       If your object name collides with an internal class, specify
     *       your module manually.
     */
    function addModule($module) {
        if (is_string($module)) {
            $original_module = $module;
            $ok = false;
            foreach ($this->prefixes as $prefix) {
                $module = $prefix . $original_module;
                if ($this->_classExists($module)) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $module = $original_module;
                if (!$this->_classExists($module)) {
                    trigger_error($original_module . ' module does not exist',
                        E_USER_ERROR);
                    return;
                }
            }
            $module = new $module();
        }
        $module->order = $this->counter++; // assign then increment
        $this->modules[$module->name] = $module;
        if ($this->autoDoctype !== false && $this->autoCollection !== false) {
            $this->collections[$this->autoCollection][$this->autoDoctype][] = $module->name;
        }
    }
    
    /**
     * Safely tests for class existence without invoking __autoload in PHP5
     * @param $name String class name to test
     * @private
     */
    function _classExists($name) {
        static $is_php_4 = null;
        if ($is_php_4 === null) {
            $is_php_4 = version_compare(PHP_VERSION, '5', '<');
        }
        if ($is_php_4) {
            return class_exists($name);
        } else {
            return class_exists($name, false);
        }
    }
    
    /**
     * Makes a collection active, while also making it valid if not
     * already done so. See $activeModules for the semantics of "active".
     * @param $collection_name Name of collection to activate
     */
    function makeCollectionActive($collection_name) {
        if (!in_array($collection_name, $this->validCollections)) {
            $this->makeCollectionValid($collection_name);
        }
        $this->activeCollections[] = $collection_name;
    }
    
    /**
     * Makes a collection valid. See $validModules for the semantics of "valid"
     */
    function makeCollectionValid($collection_name) {
        $this->validCollections[] = $collection_name;
    }
    
    /**
     * Adds a class prefix that addModule() will use to resolve a
     * string name to a concrete class
     */
    function addPrefix($prefix) {
        $this->prefixes[] = (string) $prefix;
    }
    
    function setup($config) {
        
        // load up the autocollection
        if ($this->autoCollection !== false) {
            $this->makeCollectionActive($this->autoCollection);
        }
        
        // retrieve the doctype
        $this->doctype = $this->getDoctype($config);
        if (isset($this->doctypeAliases[$this->doctype])) {
            $this->doctype = $this->doctypeAliases[$this->doctype];
        }
        
        // process module collections to module name => module instance form
        foreach ($this->collections as $col_i => $x) {
            $this->processCollections($this->collections[$col_i]);
        }
        
        $this->validModules  = $this->assembleModules($this->validCollections);
        $this->activeModules = $this->assembleModules($this->activeCollections);
        
        // setup lookup table based on all valid modules
        foreach ($this->validModules as $module) {
            foreach ($module->info as $name => $def) {
                if (!isset($this->elementLookup[$name])) {
                    $this->elementLookup[$name] = array();
                }
                $this->elementLookup[$name][] = $module->name;
            }
        }
        
        // note the different choice
        $this->contentSets = new HTMLPurifier_ContentSets(
            // content models that contain non-allowed elements are 
            // harmless because RemoveForeignElements will ensure
            // they never get in anyway, and there is usually no
            // reason why you should want to restrict a content
            // model beyond what is mandated by the doctype.
            // Note, however, that this means redefinitions of
            // content models can't be tossed in validModels willy-nilly:
            // that stuff still is regulated by configuration.
            $this->validModules
        );
        $this->attrCollections = new HTMLPurifier_AttrCollections(
            $this->attrTypes,
            // only explicitly allowed modules are allowed to affect
            // the global attribute collections. This mean's there's
            // a distinction between loading the Bdo module, and the
            // bdo element: Bdo will enable the dir attribute on all
            // elements, while bdo will only define the bdo element,
            // which will not have an editable directionality. This might
            // catch people who are loading only elements by surprise, so
            // we should consider loading an entire module if all the
            // elements it defines are requested by the user, especially
            // if it affects the global attribute collections.
            $this->activeModules
        );
        
    }
    
    /**
     * Takes a list of collections and merges together all the defined
     * modules for the current doctype from those collections.
     * @param $collections List of collection suffixes we should grab
     *                     modules from (like 'Safe' or 'Lenient')
     */
    function assembleModules($collections) {
        $modules = array();
        $numOfCollectionsUsed = 0;
        foreach ($collections as $name) {
            $disable_global = false;
            if (!isset($this->collections[$name])) {
                trigger_error("$name collection is undefined", E_USER_ERROR);
                continue;
            }
            $cols = $this->collections[$name];
            if (isset($cols[$this->doctype])) {
                if (isset($cols[$this->doctype]['*'])) {
                    unset($cols[$this->doctype]['*']);
                    $disable_global = true;
                }
                $modules += $cols[$this->doctype];
                $numOfCollectionsUsed++;
            }
            // accept catch-all doctype
            if (
                $this->doctype !== '*' && 
                isset($cols['*']) &&
                !$disable_global
            ) {
                $modules += $cols['*'];
            }
        }
        
        if ($numOfCollectionsUsed < 1) {
            // possible XSS injection if user-specified doctypes
            // are allowed
            trigger_error("Doctype {$this->doctype} does not exist, ".
                "check for typos (if you desire a doctype that allows ".
                "no elements, use an empty array collection)", E_USER_ERROR);
        }
        return $modules;
    }
    
    /**
     * Takes a collection and performs inclusions and substitutions for it.
     * @param $cols Reference to collections class member variable
     */
    function processCollections(&$cols) {
        
        // $cols is the set of collections
        // $col_i is the name (index) of a collection
        // $col is a collection/list of modules
        
        // perform inclusions
        foreach ($cols as $col_i => $col) {
            $seen = array();
            if (!empty($col[0]) && is_array($col[0])) {
                $seen[$col_i] = true; // recursion reporting
                $includes = $col[0];
                unset($cols[$col_i][0]); // remove inclusions value, recursion guard
            } else {
                $includes = array();
            }
            if (empty($includes)) continue;
            for ($i = 0; isset($includes[$i]); $i++) {
                $inc = $includes[$i];
                if (isset($seen[$inc])) {
                    trigger_error(
                        "Circular inclusion detected in $col_i collection",
                        E_USER_ERROR
                    );
                    continue;
                } else {
                    $seen[$inc] = true;
                }
                if (!isset($cols[$inc])) {
                    trigger_error(
                        "Collection $col_i tried to include undefined ".
                        "collection $inc", E_USER_ERROR);
                    continue;
                }
                foreach ($cols[$inc] as $module) {
                    if (is_array($module)) { // another inclusion!
                        foreach ($module as $inc2) $includes[] = $inc2;
                        continue;
                    }
                    $cols[$col_i][] = $module; // merge in the other modules
                }
            }
        }
        
        // replace with real modules, invert module from list to
        // assoc array of module name to module instance
        foreach ($cols as $col_i => $col) {
            $ignore_global = false;
            $order = array();
            foreach ($col as $module_i => $module) {
                unset($cols[$col_i][$module_i]);
                if (is_array($module)) {
                    trigger_error("Illegal inclusion array at index".
                        " $module_i found collection $col_i, inclusion".
                        " arrays must be at start of collection (index 0)",
                        E_USER_ERROR);
                    continue;
                }
                if ($module_i === '*' && $module === false) {
                    $ignore_global = true;
                    continue;
                }
                if (!isset($this->modules[$module])) {
                    trigger_error(
                        "Collection $col_i references undefined ".
                        "module $module",
                        E_USER_ERROR
                    );
                    continue;
                }
                $module = $this->modules[$module];
                $cols[$col_i][$module->name] = $module;
                $order[$module->name] = $module->order;
            }
            array_multisort(
                $order, SORT_ASC, SORT_NUMERIC, $cols[$col_i]
            );
            if ($ignore_global) $cols[$col_i]['*'] = false;
        }
        
        // delete pseudo-collections
        foreach ($cols as $col_i => $col) {
            if ($col_i[0] == '_') unset($cols[$col_i]);
        }
        
    }
    
    /**
     * Retrieves the doctype from the configuration object
     */
    function getDoctype($config) {
        $doctype = $config->get('HTML', 'Doctype');
        if ($doctype !== null) {
            return $doctype;
        }
        if (!$this->initialized) {
            // don't do HTML-oriented backwards compatibility stuff
            // use either the auto-doctype, or the catch-all doctype
            return $this->autoDoctype ? $this->autoDoctype : '*';
        }
        // this is backwards-compatibility stuff
        if ($config->get('Core', 'XHTML')) {
            $doctype = 'XHTML 1.0';
        } else {
            $doctype = 'HTML 4.01';
        }
        if ($config->get('HTML', 'Strict')) {
            $doctype .= ' Strict';
        } else {
            $doctype .= ' Transitional';
        }
        return $doctype;
    }
    
    /**
     * Retrieves merged element definitions for all active elements.
     * @note We may want to generate an elements array during setup
     *       and pass that on, because a specific combination of
     *       elements may trigger the loading of a module.
     * @param $config Instance of HTMLPurifier_Config, for determining
     *                stray elements.
     */
    function getElements($config) {
        
        $elements = array();
        foreach ($this->activeModules as $module) {
            foreach ($module->info as $name => $v) {
                if (isset($elements[$name])) continue;
                $elements[$name] = $this->getElement($name, $config);
            }
        }
        
        // standalone elements now loaded
        
        return $elements;
        
    }
    
    /**
     * Retrieves a single merged element definition
     * @param $name Name of element
     * @param $config Instance of HTMLPurifier_Config, may not be necessary.
     */
    function getElement($name, $config) {
        
        $def = false;
        
        $modules = $this->validModules;
        
        if (!isset($this->elementLookup[$name])) {
            return false;
        }
        
        foreach($this->elementLookup[$name] as $module_name) {
            
            $module = $modules[$module_name];
            $new_def = $module->info[$name];
            
            if (!$def && $new_def->standalone) {
                $def = $new_def;
            } elseif ($def) {
                $def->mergeIn($new_def);
            } else {
                // could "save it for another day":
                // non-standalone definitions that don't have a standalone
                // to merge into could be deferred to the end
                continue;
            }
            
            // attribute value expansions
            $this->attrCollections->performInclusions($def->attr);
            $this->attrCollections->expandIdentifiers($def->attr, $this->attrTypes);
            
            // descendants_are_inline, for ChildDef_Chameleon
            if (is_string($def->content_model) &&
                strpos($def->content_model, 'Inline') !== false) {
                if ($name != 'del' && $name != 'ins') {
                    // this is for you, ins/del
                    $def->descendants_are_inline = true;
                }
            }
            
            $this->contentSets->generateChildDef($def, $module);
        }
        
        return $def;
        
    }
    
}

?>
