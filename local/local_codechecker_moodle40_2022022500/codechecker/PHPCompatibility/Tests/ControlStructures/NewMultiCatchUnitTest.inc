<?php

/**
 * Valid pre-PHP 7.1.
 */
try {
   // Some code...
} catch (ExceptionType1 $e) {
   // Code to handle the exception.
} catch (ExceptionType2 $e) {
   // Same code to handle the exception.
} catch (Exception $e) {
   // ...
}

/**
 * Multi-catch - only valid in PHP 7.1+.
 */
try {
   // Some code...
} catch (ExceptionType1 | ExceptionType2 $e) {
   // Code to handle the exception.
} catch (\Exception $e) {
   // ...
}

// Don't throw errors during live code review.
try {
   // Some code...
} catch (
