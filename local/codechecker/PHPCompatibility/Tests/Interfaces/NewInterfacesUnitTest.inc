<?php

class MyCountable implements Countable {}
class MyOuterIterator implements OuterIterator {}
class MyRecursiveIterator implements RecursiveIterator {}
class MySeekableIterator implements \SeekableIterator {}
class MySerializable implements Serializable {
    public function __sleep() {}
    public function __wakeup() {}
}
class MySplObserver implements SplObserver {}
class MySplSubject implements SplSubject {}
class MyJsonSerializable implements JsonSerializable {}
class MySessionHandlerInterface implements SessionHandlerInterface {}

// Test multiple interfaces
class MyMultiple implements SplSubject, SeekableIterator, Countable {}

// Test case-insensitive matching
class MyUppercase implements COUNTABLE {}
class MyLowercase implements countable {}

// These shouldn't throw errors.
class MyJsonSerializable implements JsonSerializableSomething {}
class MyJsonSerializable implements myNameSpace\JsonSerializable {}

// Test anonymous class support.
$a = new class implements SeekableIterator {}
$b = new class implements \Serializable {
    public function __sleep() {}
    public function __wakeup() {}
}

// Additional new interfaces.
class MyTraversable implements Traversable {}
class MyDateTimeInterface implements DateTimeInterface {}
class MyThrowable implements Throwable {}

class MyClass {
	// Interfaces as typehints.
	function CountableTypeHint( Countable $a ) {}
	function OuterIteratorTypeHint( OuterIterator $a ) {}
	function RecursiveIteratorTypeHint( RecursiveIterator $a ) {}
	function SeekableIteratorTypeHint( SeekableIterator $a ) {}
	function SerializableTypeHint( Serializable $a ) {}
	function SplObserverTypeHint( SplObserver $a ) {}
	function SplSubjectTypeHint( SplSubject $a ) {}
	function JsonSerializableTypeHint( JsonSerializable $a ) {}
	function SessionHandlerInterfaceTypeHint( SessionHandlerInterface $a ) {}
	function TraversableTypeHint( Traversable $a ) {}
	function DateTimeInterfaceTypeHint( DateTimeInterface $a ) {}
	function ThrowableTypeHint( Throwable $a ) {}

	// Namespaced interfaces as typehints
	function SerializableTypeHint( \Serializable $a ) {} // Error: global namespace.
	function SplObserverTypeHint( myNameSpace\SplObserver $a ) {} // Ok.
	function SplSubjectTypeHint( \some\other\SplSubject $a ) {} // Ok.

	// Interfaces as nullable typehints (PHP 7.1+).
	function TraversableTypeHint( ?Traversable $a ) {}
	function DateTimeInterfaceTypeHint( ?\DateTimeInterface $a ) {}
	function ThrowableTypeHint( ?Throwable $a ) {}
	
	// Function with multiple typehinted parameters.
	function MultipleTypeHints (OuterIterator $a, ?int $b, SplObserver $c, ?\RecursiveIterator $d) {}
}

// Interfaces in type hints in anonymous functions.
function ( SplSubject $a ) {}
function(\Serializable $a) {}
function ( ?Traversable $a ) {}
function(myNameSpace\SplObserver $a) {} // Ok.

// Additional new interfaces.
class MyReflector implements Reflector {}

// Test recognition of return type declarations.
function CountableReturnTypeHint( $a ) : COUNTABLE {}
function TraversableReturnTypeHint( $a ) : ?Traversable {}
function DateTimeInterfaceReturnTypeHint( $a ) : \DateTimeInterface {}
function($a):throwable {}

// Test against false positives for return type declarations.
function SeekableIteratorReturnTypeHint( $a ) : SomeNS\SeekableIterator {}
function SessionHandlerInterfaceReturnTypeHint( $a ) : ?\MyNS\SessionHandlerInterface {}
function SplSubjectReturnTypeHint( $a ) : \MyNS\SplSubject\AnotherInterface {}

// More new interfaces.
class MySessionIdInterface implements SessionIdInterface {}
class MySessionUpdateTimestampHandlerInterface implements SessionUpdateTimestampHandlerInterface {}
