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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SearchMigratableResourcesRequest extends \Google\Model
{
  /**
   * A filter for your search. You can use the following types of filters: *
   * Resource type filters. The following strings filter for a specific type of
   * MigratableResource: * `ml_engine_model_version:*` * `automl_model:*` *
   * `automl_dataset:*` * `data_labeling_dataset:*` * "Migrated or not" filters.
   * The following strings filter for resources that either have or have not
   * already been migrated: * `last_migrate_time:*` filters for migrated
   * resources. * `NOT last_migrate_time:*` filters for not yet migrated
   * resources.
   *
   * @var string
   */
  public $filter;
  /**
   * The standard page size. The default and maximum value is 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The standard page token.
   *
   * @var string
   */
  public $pageToken;

  /**
   * A filter for your search. You can use the following types of filters: *
   * Resource type filters. The following strings filter for a specific type of
   * MigratableResource: * `ml_engine_model_version:*` * `automl_model:*` *
   * `automl_dataset:*` * `data_labeling_dataset:*` * "Migrated or not" filters.
   * The following strings filter for resources that either have or have not
   * already been migrated: * `last_migrate_time:*` filters for migrated
   * resources. * `NOT last_migrate_time:*` filters for not yet migrated
   * resources.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The standard page size. The default and maximum value is 100.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The standard page token.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchMigratableResourcesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchMigratableResourcesRequest');
