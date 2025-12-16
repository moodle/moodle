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

namespace Google\Service\Dfareporting;

class DependentFieldValue extends \Google\Model
{
  /**
   * Optional. The ID of the element that value's field will match against.
   *
   * @var string
   */
  public $elementId;
  /**
   * Optional. The field id of the dependent field.
   *
   * @var int
   */
  public $fieldId;

  /**
   * Optional. The ID of the element that value's field will match against.
   *
   * @param string $elementId
   */
  public function setElementId($elementId)
  {
    $this->elementId = $elementId;
  }
  /**
   * @return string
   */
  public function getElementId()
  {
    return $this->elementId;
  }
  /**
   * Optional. The field id of the dependent field.
   *
   * @param int $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return int
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DependentFieldValue::class, 'Google_Service_Dfareporting_DependentFieldValue');
