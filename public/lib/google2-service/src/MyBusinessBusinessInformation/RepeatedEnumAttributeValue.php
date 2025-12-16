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

namespace Google\Service\MyBusinessBusinessInformation;

class RepeatedEnumAttributeValue extends \Google\Collection
{
  protected $collection_key = 'unsetValues';
  /**
   * Enum values that are set.
   *
   * @var string[]
   */
  public $setValues;
  /**
   * Enum values that are unset.
   *
   * @var string[]
   */
  public $unsetValues;

  /**
   * Enum values that are set.
   *
   * @param string[] $setValues
   */
  public function setSetValues($setValues)
  {
    $this->setValues = $setValues;
  }
  /**
   * @return string[]
   */
  public function getSetValues()
  {
    return $this->setValues;
  }
  /**
   * Enum values that are unset.
   *
   * @param string[] $unsetValues
   */
  public function setUnsetValues($unsetValues)
  {
    $this->unsetValues = $unsetValues;
  }
  /**
   * @return string[]
   */
  public function getUnsetValues()
  {
    return $this->unsetValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepeatedEnumAttributeValue::class, 'Google_Service_MyBusinessBusinessInformation_RepeatedEnumAttributeValue');
