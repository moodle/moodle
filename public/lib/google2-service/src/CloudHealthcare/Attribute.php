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

namespace Google\Service\CloudHealthcare;

class Attribute extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Indicates the name of an attribute defined in the consent store.
   *
   * @var string
   */
  public $attributeDefinitionId;
  /**
   * Required. The value of the attribute. Must be an acceptable value as
   * defined in the consent store. For example, if the consent store defines
   * "data type" with acceptable values "questionnaire" and "step-count", when
   * the attribute name is data type, this field must contain one of those
   * values.
   *
   * @var string[]
   */
  public $values;

  /**
   * Indicates the name of an attribute defined in the consent store.
   *
   * @param string $attributeDefinitionId
   */
  public function setAttributeDefinitionId($attributeDefinitionId)
  {
    $this->attributeDefinitionId = $attributeDefinitionId;
  }
  /**
   * @return string
   */
  public function getAttributeDefinitionId()
  {
    return $this->attributeDefinitionId;
  }
  /**
   * Required. The value of the attribute. Must be an acceptable value as
   * defined in the consent store. For example, if the consent store defines
   * "data type" with acceptable values "questionnaire" and "step-count", when
   * the attribute name is data type, this field must contain one of those
   * values.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attribute::class, 'Google_Service_CloudHealthcare_Attribute');
