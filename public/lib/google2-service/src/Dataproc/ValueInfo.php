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

namespace Google\Service\Dataproc;

class ValueInfo extends \Google\Model
{
  /**
   * Annotation, comment or explanation why the property was set.
   *
   * @var string
   */
  public $annotation;
  /**
   * Optional. Value which was replaced by the corresponding component.
   *
   * @var string
   */
  public $overriddenValue;
  /**
   * Property value.
   *
   * @var string
   */
  public $value;

  /**
   * Annotation, comment or explanation why the property was set.
   *
   * @param string $annotation
   */
  public function setAnnotation($annotation)
  {
    $this->annotation = $annotation;
  }
  /**
   * @return string
   */
  public function getAnnotation()
  {
    return $this->annotation;
  }
  /**
   * Optional. Value which was replaced by the corresponding component.
   *
   * @param string $overriddenValue
   */
  public function setOverriddenValue($overriddenValue)
  {
    $this->overriddenValue = $overriddenValue;
  }
  /**
   * @return string
   */
  public function getOverriddenValue()
  {
    return $this->overriddenValue;
  }
  /**
   * Property value.
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
class_alias(ValueInfo::class, 'Google_Service_Dataproc_ValueInfo');
