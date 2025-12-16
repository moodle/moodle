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

class GoogleChromePolicyVersionsV1PolicySchemaRequiredItems extends \Google\Collection
{
  protected $collection_key = 'requiredFields';
  /**
   * The value(s) of the field that provoke required field enforcement. An empty
   * field_conditions implies that any value assigned to this field will provoke
   * required field enforcement.
   *
   * @var string[]
   */
  public $fieldConditions;
  /**
   * The fields that are required as a consequence of the field conditions.
   *
   * @var string[]
   */
  public $requiredFields;

  /**
   * The value(s) of the field that provoke required field enforcement. An empty
   * field_conditions implies that any value assigned to this field will provoke
   * required field enforcement.
   *
   * @param string[] $fieldConditions
   */
  public function setFieldConditions($fieldConditions)
  {
    $this->fieldConditions = $fieldConditions;
  }
  /**
   * @return string[]
   */
  public function getFieldConditions()
  {
    return $this->fieldConditions;
  }
  /**
   * The fields that are required as a consequence of the field conditions.
   *
   * @param string[] $requiredFields
   */
  public function setRequiredFields($requiredFields)
  {
    $this->requiredFields = $requiredFields;
  }
  /**
   * @return string[]
   */
  public function getRequiredFields()
  {
    return $this->requiredFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicySchemaRequiredItems::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicySchemaRequiredItems');
