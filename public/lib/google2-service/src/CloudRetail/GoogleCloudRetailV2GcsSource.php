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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2GcsSource extends \Google\Collection
{
  protected $collection_key = 'inputUris';
  /**
   * The schema to use when parsing the data from the source. Supported values
   * for product imports: * `product` (default): One JSON Product per line. Each
   * product must have a valid Product.id. * `product_merchant_center`: See
   * [Importing catalog data from Merchant
   * Center](https://cloud.google.com/retail/recommendations-ai/docs/upload-
   * catalog#mc). Supported values for user events imports: * `user_event`
   * (default): One JSON UserEvent per line. * `user_event_ga360`: Using
   * https://support.google.com/analytics/answer/3437719. Supported values for
   * control imports: * `control` (default): One JSON Control per line.
   * Supported values for catalog attribute imports: * `catalog_attribute`
   * (default): One CSV CatalogAttribute per line.
   *
   * @var string
   */
  public $dataSchema;
  /**
   * Required. Google Cloud Storage URIs to input files. URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files, and each file can be up to 2 GB. See [Importing product
   * information](https://cloud.google.com/retail/recommendations-
   * ai/docs/upload-catalog) for the expected file format and setup
   * instructions.
   *
   * @var string[]
   */
  public $inputUris;

  /**
   * The schema to use when parsing the data from the source. Supported values
   * for product imports: * `product` (default): One JSON Product per line. Each
   * product must have a valid Product.id. * `product_merchant_center`: See
   * [Importing catalog data from Merchant
   * Center](https://cloud.google.com/retail/recommendations-ai/docs/upload-
   * catalog#mc). Supported values for user events imports: * `user_event`
   * (default): One JSON UserEvent per line. * `user_event_ga360`: Using
   * https://support.google.com/analytics/answer/3437719. Supported values for
   * control imports: * `control` (default): One JSON Control per line.
   * Supported values for catalog attribute imports: * `catalog_attribute`
   * (default): One CSV CatalogAttribute per line.
   *
   * @param string $dataSchema
   */
  public function setDataSchema($dataSchema)
  {
    $this->dataSchema = $dataSchema;
  }
  /**
   * @return string
   */
  public function getDataSchema()
  {
    return $this->dataSchema;
  }
  /**
   * Required. Google Cloud Storage URIs to input files. URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files, and each file can be up to 2 GB. See [Importing product
   * information](https://cloud.google.com/retail/recommendations-
   * ai/docs/upload-catalog) for the expected file format and setup
   * instructions.
   *
   * @param string[] $inputUris
   */
  public function setInputUris($inputUris)
  {
    $this->inputUris = $inputUris;
  }
  /**
   * @return string[]
   */
  public function getInputUris()
  {
    return $this->inputUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2GcsSource::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2GcsSource');
