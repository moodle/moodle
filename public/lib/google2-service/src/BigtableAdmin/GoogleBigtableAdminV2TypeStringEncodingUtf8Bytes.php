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

namespace Google\Service\BigtableAdmin;

class GoogleBigtableAdminV2TypeStringEncodingUtf8Bytes extends \Google\Model
{
  /**
   * Single-character escape sequence used to support NULL values. If set,
   * allows NULL values to be encoded as the empty string "". The actual empty
   * string, or any value where every character equals `null_escape_char`, has
   * one more `null_escape_char` appended. If `null_escape_char` is set and does
   * not equal the ASCII null character `0x00`, then the encoding will not
   * support sorted mode. .
   *
   * @var string
   */
  public $nullEscapeChar;

  /**
   * Single-character escape sequence used to support NULL values. If set,
   * allows NULL values to be encoded as the empty string "". The actual empty
   * string, or any value where every character equals `null_escape_char`, has
   * one more `null_escape_char` appended. If `null_escape_char` is set and does
   * not equal the ASCII null character `0x00`, then the encoding will not
   * support sorted mode. .
   *
   * @param string $nullEscapeChar
   */
  public function setNullEscapeChar($nullEscapeChar)
  {
    $this->nullEscapeChar = $nullEscapeChar;
  }
  /**
   * @return string
   */
  public function getNullEscapeChar()
  {
    return $this->nullEscapeChar;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleBigtableAdminV2TypeStringEncodingUtf8Bytes::class, 'Google_Service_BigtableAdmin_GoogleBigtableAdminV2TypeStringEncodingUtf8Bytes');
