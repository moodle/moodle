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

class GoogleCloudDatapipelinesV1ComputeSchemaRequest extends \Google\Collection
{
  protected $collection_key = 'inputSchemas';
  protected $configType = GoogleCloudDatapipelinesV1ConfiguredTransform::class;
  protected $configDataType = '';
  protected $inputSchemasType = GoogleCloudDatapipelinesV1Schema::class;
  protected $inputSchemasDataType = 'array';
  protected $rawSchemaType = GoogleCloudDatapipelinesV1RawSchemaInfo::class;
  protected $rawSchemaDataType = '';

  /**
   * @param GoogleCloudDatapipelinesV1ConfiguredTransform
   */
  public function setConfig(GoogleCloudDatapipelinesV1ConfiguredTransform $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudDatapipelinesV1ConfiguredTransform
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * @param GoogleCloudDatapipelinesV1Schema[]
   */
  public function setInputSchemas($inputSchemas)
  {
    $this->inputSchemas = $inputSchemas;
  }
  /**
   * @return GoogleCloudDatapipelinesV1Schema[]
   */
  public function getInputSchemas()
  {
    return $this->inputSchemas;
  }
  /**
   * @param GoogleCloudDatapipelinesV1RawSchemaInfo
   */
  public function setRawSchema(GoogleCloudDatapipelinesV1RawSchemaInfo $rawSchema)
  {
    $this->rawSchema = $rawSchema;
  }
  /**
   * @return GoogleCloudDatapipelinesV1RawSchemaInfo
   */
  public function getRawSchema()
  {
    return $this->rawSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1ComputeSchemaRequest::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1ComputeSchemaRequest');
