<?php

class NullableTypes {

    /**
     * Pre PHP 7.1 return types.
     */
    function testReturnBool(): bool {}
    function testReturnInt(): int {}
    function testReturnFloat(): float {}
    function testReturnString(): string {}
    function testReturnArray(): array {}
    function testReturnCallable(): callable {}
    function testReturnSelf(): self {}
    function testReturnObject(): Baz {}
    function testReturnNamespacedObject(): \myNamespace\Baz {}

    /**
     * PHP 7.1: New nullable return types.
     */
    function testReturnNullableBool(): ?bool {}
    function testReturnNullableInt(): ?int {}
    function testReturnNullableFloat(): ?float {}
    function testReturnNullableString(): ?string {}
    function testReturnNullableArray(): ?array {}
    function testReturnNullableCallable(): ?callable {}
    function testReturnNullableSelf(): ?self {}
    function testReturnNullableObject(): ?Baz {}
    function testReturnNamespacedObject(): ?\myNamespace\Baz {}


    /**
     * Pre PHP 7.1 type hints.
     */
    function testTypeHintBool(bool $nullable) {}
    function testTypeHintInt(int $nullable) {}
    function testTypeHintFloat(float $nullable) {}
    function testTypeHintString(string $nullable) {}
    function testTypeHintArray(array $nullable) {}
    function testTypeHintCallable(callable $nullable) {}
    function testTypeHintSelf(self $nullable) {}
    function testTypeHintObject(Baz $nullable) {}
    function testTypeHintNamespacedObject(\myNamespace\Baz $nullable) {}

    /**
     * PHP 7.1: Nullable type hints.
     */
    function testTypeHintNullableBool(?bool $nullable) {}
    function testTypeHintNullableInt(?int $nullable) {}
    function testTypeHintNullableFloat(?float $nullable) {}
    function testTypeHintNullableString(?string $nullable) {}
    function testTypeHintNullableArray(?array $nullable) {}
    function testTypeHintNullableCallable(?callable $nullable) {}
    function testTypeHintNullableSelf(?self $nullable) {}
    function testTypeHintNullableObject(?Baz $nullable) {}
    function testTypeHintNullableNamespacedObject(?\myNamespace\Baz $nullable) {}

    // Test with multiple variables and different spacing.
    function testTypeHintNullableMultiParam( ?bool $nullableA, ?int $nullableB, ?Baz $nullableC ) {}
}

// Test closures with nullable types.
function (): ?string {}
function(?callable $nullable) {}

function testTypeDeclarationsInterspersedWithComments(
	// This is a parameter declaration.
	?string $nullable,
	// phpcs:ignore Standard.Category.Sniff -- ignore something about a param type declaration.
	?
	/* Comment. */
	\myNamespace\
	// Comment.
	Baz $nullable
) :
	// Comment.
	?
	// phpcs:ignore Standard.Category.Sniff -- ignore something about a return type declaration.
	\myNamespace\
	/* Comment. */
	Baz
{
}
