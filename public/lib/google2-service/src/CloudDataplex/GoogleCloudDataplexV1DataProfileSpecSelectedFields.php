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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileSpecSelectedFields extends \Google\Collection
{
  protected $collection_key = 'fieldNames';
  /**
   * Optional. Expected input is a list of fully qualified names of fields as in
   * the schema.Only top-level field names for nested fields are supported. For
   * instance, if 'x' is of nested field type, listing 'x' is supported but
   * 'x.y.z' is not supported. Here 'y' and 'y.z' are nested fields of 'x'.
   *
   * @var string[]
   */
  public $fieldNames;

  /**
   * Optional. Expected input is a list of fully qualified names of fields as in
   * the schema.Only top-level field names for nested fields are supported. For
   * instance, if 'x' is of nested field type, listing 'x' is supported but
   * 'x.y.z' is not supported. Here 'y' and 'y.z' are nested fields of 'x'.
   *
   * @param string[] $fieldNames
   */
  public function setFieldNames($fieldNames)
  {
    $this->fieldNames = $fieldNames;
  }
  /**
   * @return string[]
   */
  public function getFieldNames()
  {
    return $this->fieldNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileSpecSelectedFields::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileSpecSelectedFields');
