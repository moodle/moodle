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

class UserPhone extends \Google\Model
{
  /**
   * Custom Type.
   *
   * @var string
   */
  public $customType;
  /**
   * If this is user's primary phone or not.
   *
   * @var bool
   */
  public $primary;
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example phone could be of home_fax work mobile etc. In addition to the
   * standard type an entry can have a custom type and can give it any name.
   * Such types should have the CUSTOM value as type and also have a customType
   * value.
   *
   * @var string
   */
  public $type;
  /**
   * Phone number.
   *
   * @var string
   */
  public $value;

  /**
   * Custom Type.
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
   * If this is user's primary phone or not.
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
   * Each entry can have a type which indicates standard types of that entry.
   * For example phone could be of home_fax work mobile etc. In addition to the
   * standard type an entry can have a custom type and can give it any name.
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
  /**
   * Phone number.
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
class_alias(UserPhone::class, 'Google_Service_Directory_UserPhone');
