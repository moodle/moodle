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

class GoogleCloudDatapipelinesV1FieldType extends \Google\Model
{
  protected $collectionElementTypeType = GoogleCloudDatapipelinesV1FieldType::class;
  protected $collectionElementTypeDataType = '';
  protected $logicalTypeType = GoogleCloudDatapipelinesV1LogicalType::class;
  protected $logicalTypeDataType = '';
  protected $mapTypeType = GoogleCloudDatapipelinesV1MapType::class;
  protected $mapTypeDataType = '';
  /**
   * @var bool
   */
  public $nullable;
  protected $rowSchemaType = GoogleCloudDatapipelinesV1Schema::class;
  protected $rowSchemaDataType = '';
  /**
   * @var string
   */
  public $type;

  /**
   * @param GoogleCloudDatapipelinesV1FieldType
   */
  public function setCollectionElementType(GoogleCloudDatapipelinesV1FieldType $collectionElementType)
  {
    $this->collectionElementType = $collectionElementType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldType
   */
  public function getCollectionElementType()
  {
    return $this->collectionElementType;
  }
  /**
   * @param GoogleCloudDatapipelinesV1LogicalType
   */
  public function setLogicalType(GoogleCloudDatapipelinesV1LogicalType $logicalType)
  {
    $this->logicalType = $logicalType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1LogicalType
   */
  public function getLogicalType()
  {
    return $this->logicalType;
  }
  /**
   * @param GoogleCloudDatapipelinesV1MapType
   */
  public function setMapType(GoogleCloudDatapipelinesV1MapType $mapType)
  {
    $this->mapType = $mapType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1MapType
   */
  public function getMapType()
  {
    return $this->mapType;
  }
  /**
   * @param bool
   */
  public function setNullable($nullable)
  {
    $this->nullable = $nullable;
  }
  /**
   * @return bool
   */
  public function getNullable()
  {
    return $this->nullable;
  }
  /**
   * @param GoogleCloudDatapipelinesV1Schema
   */
  public function setRowSchema(GoogleCloudDatapipelinesV1Schema $rowSchema)
  {
    $this->rowSchema = $rowSchema;
  }
  /**
   * @return GoogleCloudDatapipelinesV1Schema
   */
  public function getRowSchema()
  {
    return $this->rowSchema;
  }
  /**
   * @param string
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1FieldType::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1FieldType');
