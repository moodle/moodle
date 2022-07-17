<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore

// These are ok declarations. https://www.php-fig.org/psr/psr-12/#46-abstract-final-and-static .
abstract class goodclass {
    private function private_function() { }
    protected function protected_function() { }
    public function public_function() { }

    private static function private_static_function() { }
    protected static function protected_static_function() { }
    public static function public_static_function() { }

    final private function final_private_function() { }
    final protected function final_protected_function() { }
    final public function final_public_function() { }

    final private static function final_private_static_function() { }
    final protected static function final_protected_static_function() { }
    final public static function final_public_static_function() { }

    abstract protected function abstract_protected_function();
    abstract public function abstract_public_function();

    abstract protected static function abstract_protected_static_function();
    abstract public static function abstract_public_static_function();
}

// These are wrong declarations.
abstract class badclass {
    static private function private_static_function() { }
    static protected function protected_static_function() { }
    static public function public_static_function() { }

    private final function final_private_function() { }
    protected final function final_protected_function() { }
    public final function final_public_function() { }

    static private final function final_private_static_function() { }
    static protected final function final_protected_static_function() { }
    static public final function final_public_static_function() { }

    protected abstract function abstract_protected_function();
    public abstract function abstract_public_function();

    static protected abstract function abstract_protected_static_function();
    static public abstract function abstract_public_static_function();
}
