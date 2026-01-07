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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1Hash extends \Google\Model
{
  /**
   * No hash requested.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * Use a sha256 hash.
   */
  public const TYPE_SHA256 = 'SHA256';
  /**
   * Use a md5 hash.
   */
  public const TYPE_MD5 = 'MD5';
  /**
   * Dirhash of a Go module's source code which is then hex-encoded.
   */
  public const TYPE_GO_MODULE_H1 = 'GO_MODULE_H1';
  /**
   * Use a sha512 hash.
   */
  public const TYPE_SHA512 = 'SHA512';
  /**
   * The type of hash that was performed.
   *
   * @var string
   */
  public $type;
  /**
   * The hash value.
   *
   * @var string
   */
  public $value;

  /**
   * The type of hash that was performed.
   *
   * Accepted values: NONE, SHA256, MD5, GO_MODULE_H1, SHA512
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The hash value.
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
class_alias(GoogleDevtoolsCloudbuildV1Hash::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Hash');
