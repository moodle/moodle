<?php

/**
 * Represents a document type, contains information on which modules
 * need to be loaded.
 * @note This class is inspected by Printer_HTMLDefinition->renderDoctype.
 *       If structure changes, please update that function.
 */
class HTMLPurifier_Doctype
{
    /**
     * Full name of doctype
     */
    var $name;
    
    /**
     * List of standard modules (string identifiers or literal objects)
     * that this doctype uses
     */
    var $modules = array();
    
    /**
     * List of modules to use for tidying up code
     */
    var $tidyModules = array();
    
    /**
     * Is the language derived from XML (i.e. XHTML)?
     */
    var $xml = true;
    
    /**
     * List of aliases for this doctype
     */
    var $aliases = array();
    
    /**
     * Public DTD identifier
     */
    var $dtdPublic;
    
    /**
     * System DTD identifier
     */
    var $dtdSystem;
    
    function HTMLPurifier_Doctype($name = null, $xml = true, $modules = array(),
        $tidyModules = array(), $aliases = array(), $dtd_public = null, $dtd_system = null
    ) {
        $this->name         = $name;
        $this->xml          = $xml;
        $this->modules      = $modules;
        $this->tidyModules  = $tidyModules;
        $this->aliases      = $aliases;
        $this->dtdPublic    = $dtd_public;
        $this->dtdSystem    = $dtd_system;
    }
    
    /**
     * Clones the doctype, use before resolving modes and the like
     */
    function copy() {
        return unserialize(serialize($this));
    }
}

