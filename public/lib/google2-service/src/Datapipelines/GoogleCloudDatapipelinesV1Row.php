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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1Row extends \Google\Collection
{
  protected $collection_key = 'values';
  protected $schemaType = GoogleCloudDatapipelinesV1SchemaSource::class;
  protected $schemaDataType = '';
  protected $valuesType = GoogleCloudDatapipelinesV1FieldValue::class;
  protected $valuesDataType = 'array';

  /**
   * @param GoogleCloudDatapipelinesV1SchemaSource
   */
  public function setSchema(GoogleCloudDatapipelinesV1SchemaSource $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudDatapipelinesV1SchemaSource
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * @param GoogleCloudDatapipelinesV1FieldValue[]
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1Row::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1Row');
