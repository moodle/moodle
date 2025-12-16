<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Playdeveloperreporting;

class GoogleTypeDecimal extends \Google\Model
{
  /**
   * The decimal value, as a string. The string representation consists of an
   * optional sign, `+` (`U+002B`) or `-` (`U+002D`), followed by a sequence of
   * zero or more decimal digits ("the integer"), optionally followed by a
   * fraction, optionally followed by an exponent. An empty string **should** be
   * interpreted as `0`. The fraction consists of a decimal point followed by
   * zero or more decimal digits. The string must contain at least one digit in
   * either the integer or the fraction. The number formed by the sign, the
   * integer and the fraction is referred to as the significand. The exponent
   * consists of the character `e` (`U+0065`) or `E` (`U+0045`) followed by one
   * or more decimal digits. Services **should** normalize decimal values before
   * storing them by: - Removing an explicitly-provided `+` sign (`+2.5` ->
   * `2.5`). - Replacing a zero-length integer value with `0` (`.5` -> `0.5`). -
   * Coercing the exponent character to upper-case, with explicit sign (`2.5e8`
   * -> `2.5E+8`). - Removing an explicitly-provided zero exponent (`2.5E0` ->
   * `2.5`). Services **may** perform additional normalization based on its own
   * needs and the internal decimal implementation selected, such as shifting
   * the decimal point and exponent value together (example: `2.5E-1` <->
   * `0.25`). Additionally, services **may** preserve trailing zeroes in the
   * fraction to indicate increased precision, but are not required to do so.
   * Note that only the `.` character is supported to divide the integer and the
   * fraction; `,` **should not** be supported regardless of locale.
   * Additionally, thousand separators **should not** be supported. If a service
   * does support them, values **must** be normalized. The ENBF grammar is:
   * DecimalString = '' | [Sign] Significand [Exponent]; Sign = '+' | '-';
   * Significand = Digits '.' | [Digits] '.' Digits; Exponent = ('e' | 'E')
   * [Sign] Digits; Digits = { '0' | '1' | '2' | '3' | '4' | '5' | '6' | '7' |
   * '8' | '9' }; Services **should** clearly document the range of supported
   * values, the maximum supported precision (total number of digits), and, if
   * applicable, the scale (number of digits after the decimal point), as well
   * as how it behaves when receiving out-of-bounds values. Services **may**
   * choose to accept values passed as input even when the value has a higher
   * precision or scale than the service supports, and **should** round the
   * value to fit the supported scale. Alternatively, the service **may** error
   * with `400 Bad Request` (`INVALID_ARGUMENT` in gRPC) if precision would be
   * lost. Services **should** error with `400 Bad Request` (`INVALID_ARGUMENT`
   * in gRPC) if the service receives a value outside of the supported range.
   *
   * @var string
   */
  public $value;

  /**
   * The decimal value, as a string. The string representation consists of an
   * optional sign, `+` (`U+002B`) or `-` (`U+002D`), followed by a sequence of
   * zero or more decimal digits ("the integer"), optionally followed by a
   * fraction, optionally followed by an exponent. An empty string **should** be
   * interpreted as `0`. The fraction consists of a decimal point followed by
   * zero or more decimal digits. The string must contain at least one digit in
   * either the integer or the fraction. The number formed by the sign, the
   * integer and the fraction is referred to as the significand. The exponent
   * consists of the character `e` (`U+0065`) or `E` (`U+0045`) followed by one
   * or more decimal digits. Services **should** normalize decimal values before
   * storing them by: - Removing an explicitly-provided `+` sign (`+2.5` ->
   * `2.5`). - Replacing a zero-length integer value with `0` (`.5` -> `0.5`). -
   * Coercing the exponent character to upper-case, with explicit sign (`2.5e8`
   * -> `2.5E+8`). - Removing an explicitly-provided zero exponent (`2.5E0` ->
   * `2.5`). Services **may** perform additional normalization based on its own
   * needs and the internal decimal implementation selected, such as shifting
   * the decimal point and exponent value together (example: `2.5E-1` <->
   * `0.25`). Additionally, services **may** preserve trailing zeroes in the
   * fraction to indicate increased precision, but are not required to do so.
   * Note that only the `.` character is supported to divide the integer and the
   * fraction; `,` **should not** be supported regardless of locale.
   * Additionally, thousand separators **should not** be supported. If a service
   * does support them, values **must** be normalized. The ENBF grammar is:
   * DecimalString = '' | [Sign] Significand [Exponent]; Sign = '+' | '-';
   * Significand = Digits '.' | [Digits] '.' Digits; Exponent = ('e' | 'E')
   * [Sign] Digits; Digits = { '0' | '1' | '2' | '3' | '4' | '5' | '6' | '7' |
   * '8' | '9' }; Services **should** clearly document the range of supported
   * values, the maximum supported precision (total number of digits), and, if
   * applicable, the scale (number of digits after the decimal point), as well
   * as how it behaves when receiving out-of-bounds values. Services **may**
   * choose to accept values passed as input even when the value has a higher
   * precision or scale than the service supports, and **should** round the
   * value to fit the supported scale. Alternatively, the service **may** error
   * with `400 Bad Request` (`INVALID_ARGUMENT` in gRPC) if precision would be
   * lost. Services **should** error with `400 Bad Request` (`INVALID_ARGUMENT`
   * in gRPC) if the service receives a value outside of the supported range.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleTypeDecimal::class, 'Google_Service_Playdeveloperreporting_GoogleTypeDecimal');
