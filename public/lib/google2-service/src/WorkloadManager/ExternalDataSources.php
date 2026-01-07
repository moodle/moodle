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

namespace Google\Service\WorkloadManager;

class ExternalDataSources extends \Google\Model
{
  /**
   * Unknown type
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * BigQuery table
   */
  public const TYPE_BIG_QUERY_TABLE = 'BIG_QUERY_TABLE';
  /**
   * Required. The asset type of the external data source this can be one of
   * go/cai-asset-types to override the default asset type or it can be a custom
   * type defined by the user custom type must match the asset type in the rule
   *
   * @var string
   */
  public $assetType;
  /**
   * Optional. Name of external data source. The name will be used inside the
   * rego/sql to refer the external data
   *
   * @var string
   */
  public $name;
  /**
   * Required. Type of external data source
   *
   * @var string
   */
  public $type;
  /**
   * Required. URI of external data source. example of bq table
   * {project_ID}.{dataset_ID}.{table_ID}
   *
   * @var string
   */
  public $uri;

  /**
   * Required. The asset type of the external data source this can be one of
   * go/cai-asset-types to override the default asset type or it can be a custom
   * type defined by the user custom type must match the asset type in the rule
   *
   * @param string $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return string
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * Optional. Name of external data source. The name will be used inside the
   * rego/sql to refer the external data
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
   * Required. Type of external data source
   *
   * Accepted values: TYPE_UNSPECIFIED, BIG_QUERY_TABLE
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
  /**
   * Required. URI of external data source. example of bq table
   * {project_ID}.{dataset_ID}.{table_ID}
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalDataSources::class, 'Google_Service_WorkloadManager_ExternalDataSources');
