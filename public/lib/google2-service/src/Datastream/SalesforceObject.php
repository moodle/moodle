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

namespace Google\Service\Datastream;

class SalesforceObject extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $fieldsType = SalesforceField::class;
  protected $fieldsDataType = 'array';
  /**
   * Object name.
   *
   * @var string
   */
  public $objectName;

  /**
   * Salesforce fields. When unspecified as part of include objects, includes
   * everything, when unspecified as part of exclude objects, excludes nothing.
   *
   * @param SalesforceField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return SalesforceField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Object name.
   *
   * @param string $objectName
   */
  public function setObjectName($objectName)
  {
    $this->objectName = $objectName;
  }
  /**
   * @return string
   */
  public function getObjectName()
  {
    return $this->objectName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SalesforceObject::class, 'Google_Service_Datastream_SalesforceObject');
