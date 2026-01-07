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

namespace Google\Service\Backupdr;

class Entry extends \Google\Model
{
  /**
   * Optional. Key for the metadata entry.
   *
   * @var string
   */
  public $key;
  /**
   * Optional. Value for the metadata entry. These are free-form strings, and
   * only have meaning as interpreted by the image running in the instance. The
   * only restriction placed on values is that their size must be less than or
   * equal to 262144 bytes (256 KiB).
   *
   * @var string
   */
  public $value;

  /**
   * Optional. Key for the metadata entry.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Optional. Value for the metadata entry. These are free-form strings, and
   * only have meaning as interpreted by the image running in the instance. The
   * only restriction placed on values is that their size must be less than or
   * equal to 262144 bytes (256 KiB).
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
class_alias(Entry::class, 'Google_Service_Backupdr_Entry');
