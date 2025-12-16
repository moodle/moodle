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

class GoogleCloudDataplexV1AssetResourceSpec extends \Google\Model
{
  /**
   * Access mode unspecified.
   */
  public const READ_ACCESS_MODE_ACCESS_MODE_UNSPECIFIED = 'ACCESS_MODE_UNSPECIFIED';
  /**
   * Default. Data is accessed directly using storage APIs.
   */
  public const READ_ACCESS_MODE_DIRECT = 'DIRECT';
  /**
   * Data is accessed through a managed interface using BigQuery APIs.
   */
  public const READ_ACCESS_MODE_MANAGED = 'MANAGED';
  /**
   * Type not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Cloud Storage bucket.
   */
  public const TYPE_STORAGE_BUCKET = 'STORAGE_BUCKET';
  /**
   * BigQuery dataset.
   */
  public const TYPE_BIGQUERY_DATASET = 'BIGQUERY_DATASET';
  /**
   * Immutable. Relative name of the cloud resource that contains the data that
   * is being managed within a lake. For example:
   * projects/{project_number}/buckets/{bucket_id}
   * projects/{project_number}/datasets/{dataset_id}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Determines how read permissions are handled for each asset and
   * their associated tables. Only available to storage buckets assets.
   *
   * @var string
   */
  public $readAccessMode;
  /**
   * Required. Immutable. Type of resource.
   *
   * @var string
   */
  public $type;

  /**
   * Immutable. Relative name of the cloud resource that contains the data that
   * is being managed within a lake. For example:
   * projects/{project_number}/buckets/{bucket_id}
   * projects/{project_number}/datasets/{dataset_id}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Determines how read permissions are handled for each asset and
   * their associated tables. Only available to storage buckets assets.
   *
   * Accepted values: ACCESS_MODE_UNSPECIFIED, DIRECT, MANAGED
   *
   * @param self::READ_ACCESS_MODE_* $readAccessMode
   */
  public function setReadAccessMode($readAccessMode)
  {
    $this->readAccessMode = $readAccessMode;
  }
  /**
   * @return self::READ_ACCESS_MODE_*
   */
  public function getReadAccessMode()
  {
    return $this->readAccessMode;
  }
  /**
   * Required. Immutable. Type of resource.
   *
   * Accepted values: TYPE_UNSPECIFIED, STORAGE_BUCKET, BIGQUERY_DATASET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AssetResourceSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AssetResourceSpec');
