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

class GoogleCloudAiplatformV1SearchMigratableResourcesResponse extends \Google\Collection
{
  protected $collection_key = 'migratableResources';
  protected $migratableResourcesType = GoogleCloudAiplatformV1MigratableResource::class;
  protected $migratableResourcesDataType = 'array';
  /**
   * The standard next-page token. The migratable_resources may not fill
   * page_size in SearchMigratableResourcesRequest even when there are
   * subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * All migratable resources that can be migrated to the location specified in
   * the request.
   *
   * @param GoogleCloudAiplatformV1MigratableResource[] $migratableResources
   */
  public function setMigratableResources($migratableResources)
  {
    $this->migratableResources = $migratableResources;
  }
  /**
   * @return GoogleCloudAiplatformV1MigratableResource[]
   */
  public function getMigratableResources()
  {
    return $this->migratableResources;
  }
  /**
   * The standard next-page token. The migratable_resources may not fill
   * page_size in SearchMigratableResourcesRequest even when there are
   * subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchMigratableResourcesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchMigratableResourcesResponse');
