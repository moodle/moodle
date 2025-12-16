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

namespace Google\Service\Directory;

class UserIm extends \Google\Model
{
  /**
   * Custom protocol.
   *
   * @var string
   */
  public $customProtocol;
  /**
   * Custom type.
   *
   * @var string
   */
  public $customType;
  /**
   * Instant messenger id.
   *
   * @var string
   */
  public $im;
  /**
   * If this is user's primary im. Only one entry could be marked as primary.
   *
   * @var bool
   */
  public $primary;
  /**
   * Protocol used in the instant messenger. It should be one of the values from
   * ImProtocolTypes map. Similar to type it can take a CUSTOM value and specify
   * the custom name in customProtocol field.
   *
   * @var string
   */
  public $protocol;
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example instant messengers could be of home work etc. In addition to
   * the standard type an entry can have a custom type and can take any value.
   * Such types should have the CUSTOM value as type and also have a customType
   * value.
   *
   * @var string
   */
  public $type;

  /**
   * Custom protocol.
   *
   * @param string $customProtocol
   */
  public function setCustomProtocol($customProtocol)
  {
    $this->customProtocol = $customProtocol;
  }
  /**
   * @return string
   */
  public function getCustomProtocol()
  {
    return $this->customProtocol;
  }
  /**
   * Custom type.
   *
   * @param string $customType
   */
  public function setCustomType($customType)
  {
    $this->customType = $customType;
  }
  /**
   * @return string
   */
  public function getCustomType()
  {
    return $this->customType;
  }
  /**
   * Instant messenger id.
   *
   * @param string $im
   */
  public function setIm($im)
  {
    $this->im = $im;
  }
  /**
   * @return string
   */
  public function getIm()
  {
    return $this->im;
  }
  /**
   * If this is user's primary im. Only one entry could be marked as primary.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Protocol used in the instant messenger. It should be one of the values from
   * ImProtocolTypes map. Similar to type it can take a CUSTOM value and specify
   * the custom name in customProtocol field.
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example instant messengers could be of home work etc. In addition to
   * the standard type an entry can have a custom type and can take any value.
   * Such types should have the CUSTOM value as type and also have a customType
   * value.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserIm::class, 'Google_Service_Directory_UserIm');
