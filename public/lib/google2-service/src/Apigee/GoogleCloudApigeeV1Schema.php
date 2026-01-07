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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Schema extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $dimensionsType = GoogleCloudApigeeV1SchemaSchemaElement::class;
  protected $dimensionsDataType = 'array';
  /**
   * Additional metadata associated with schema. This is a legacy field and
   * usually consists of an empty array of strings.
   *
   * @var string[]
   */
  public $meta;
  protected $metricsType = GoogleCloudApigeeV1SchemaSchemaElement::class;
  protected $metricsDataType = 'array';

  /**
   * List of schema fields grouped as dimensions.
   *
   * @param GoogleCloudApigeeV1SchemaSchemaElement[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudApigeeV1SchemaSchemaElement[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Additional metadata associated with schema. This is a legacy field and
   * usually consists of an empty array of strings.
   *
   * @param string[] $meta
   */
  public function setMeta($meta)
  {
    $this->meta = $meta;
  }
  /**
   * @return string[]
   */
  public function getMeta()
  {
    return $this->meta;
  }
  /**
   * List of schema fields grouped as dimensions that can be used with an
   * aggregate function such as `sum`, `avg`, `min`, and `max`.
   *
   * @param GoogleCloudApigeeV1SchemaSchemaElement[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudApigeeV1SchemaSchemaElement[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Schema::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Schema');
