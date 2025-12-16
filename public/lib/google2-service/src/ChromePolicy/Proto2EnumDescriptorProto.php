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

namespace Google\Service\ChromePolicy;

class Proto2EnumDescriptorProto extends \Google\Collection
{
  public const VISIBILITY_VISIBILITY_UNSET = 'VISIBILITY_UNSET';
  public const VISIBILITY_VISIBILITY_LOCAL = 'VISIBILITY_LOCAL';
  public const VISIBILITY_VISIBILITY_EXPORT = 'VISIBILITY_EXPORT';
  protected $collection_key = 'value';
  /**
   * @var string
   */
  public $name;
  protected $valueType = Proto2EnumValueDescriptorProto::class;
  protected $valueDataType = 'array';
  /**
   * Support for `export` and `local` keywords on enums.
   *
   * @var string
   */
  public $visibility;

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param Proto2EnumValueDescriptorProto[] $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return Proto2EnumValueDescriptorProto[]
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Support for `export` and `local` keywords on enums.
   *
   * Accepted values: VISIBILITY_UNSET, VISIBILITY_LOCAL, VISIBILITY_EXPORT
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Proto2EnumDescriptorProto::class, 'Google_Service_ChromePolicy_Proto2EnumDescriptorProto');
