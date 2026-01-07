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

namespace Google\Service\VMMigrationService;

class AdaptationModifier extends \Google\Model
{
  /**
   * Optional. The modifier name.
   *
   * @var string
   */
  public $modifier;
  /**
   * Optional. The value of the modifier. The actual value depends on the
   * modifier and can also be empty.
   *
   * @var string
   */
  public $value;

  /**
   * Optional. The modifier name.
   *
   * @param string $modifier
   */
  public function setModifier($modifier)
  {
    $this->modifier = $modifier;
  }
  /**
   * @return string
   */
  public function getModifier()
  {
    return $this->modifier;
  }
  /**
   * Optional. The value of the modifier. The actual value depends on the
   * modifier and can also be empty.
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
class_alias(AdaptationModifier::class, 'Google_Service_VMMigrationService_AdaptationModifier');
