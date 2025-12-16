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

class GoogleCloudAiplatformV1BatchMigrateResourcesRequest extends \Google\Collection
{
  protected $collection_key = 'migrateResourceRequests';
  protected $migrateResourceRequestsType = GoogleCloudAiplatformV1MigrateResourceRequest::class;
  protected $migrateResourceRequestsDataType = 'array';

  /**
   * Required. The request messages specifying the resources to migrate. They
   * must be in the same location as the destination. Up to 50 resources can be
   * migrated in one batch.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequest[] $migrateResourceRequests
   */
  public function setMigrateResourceRequests($migrateResourceRequests)
  {
    $this->migrateResourceRequests = $migrateResourceRequests;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequest[]
   */
  public function getMigrateResourceRequests()
  {
    return $this->migrateResourceRequests;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BatchMigrateResourcesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchMigrateResourcesRequest');
