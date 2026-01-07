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

namespace Google\Service\CloudTrace;

class TruncatableString extends \Google\Model
{
  /**
   * The number of bytes removed from the original string. If this value is 0,
   * then the string was not shortened.
   *
   * @var int
   */
  public $truncatedByteCount;
  /**
   * The shortened string. For example, if the original string is 500 bytes long
   * and the limit of the string is 128 bytes, then `value` contains the first
   * 128 bytes of the 500-byte string. Truncation always happens on a UTF8
   * character boundary. If there are multi-byte characters in the string, then
   * the length of the shortened string might be less than the size limit.
   *
   * @var string
   */
  public $value;

  /**
   * The number of bytes removed from the original string. If this value is 0,
   * then the string was not shortened.
   *
   * @param int $truncatedByteCount
   */
  public function setTruncatedByteCount($truncatedByteCount)
  {
    $this->truncatedByteCount = $truncatedByteCount;
  }
  /**
   * @return int
   */
  public function getTruncatedByteCount()
  {
    return $this->truncatedByteCount;
  }
  /**
   * The shortened string. For example, if the original string is 500 bytes long
   * and the limit of the string is 128 bytes, then `value` contains the first
   * 128 bytes of the 500-byte string. Truncation always happens on a UTF8
   * character boundary. If there are multi-byte characters in the string, then
   * the length of the shortened string might be less than the size limit.
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
class_alias(TruncatableString::class, 'Google_Service_CloudTrace_TruncatableString');
