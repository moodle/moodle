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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig extends \Google\Model
{
  /**
   * Output only. The collection in Document AI Warehouse associated with the
   * dataset.
   *
   * @var string
   */
  public $collection;
  /**
   * Output only. The schema in Document AI Warehouse associated with the
   * dataset.
   *
   * @var string
   */
  public $schema;

  /**
   * Output only. The collection in Document AI Warehouse associated with the
   * dataset.
   *
   * @param string $collection
   */
  public function setCollection($collection)
  {
    $this->collection = $collection;
  }
  /**
   * @return string
   */
  public function getCollection()
  {
    return $this->collection;
  }
  /**
   * Output only. The schema in Document AI Warehouse associated with the
   * dataset.
   *
   * @param string $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig');
