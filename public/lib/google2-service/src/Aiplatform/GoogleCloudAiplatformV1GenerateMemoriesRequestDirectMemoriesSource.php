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

class GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource extends \Google\Collection
{
  protected $collection_key = 'directMemories';
  protected $directMemoriesType = GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory::class;
  protected $directMemoriesDataType = 'array';

  /**
   * Required. The direct memories to upload to Memory Bank. At most 5 direct
   * memories are allowed per request.
   *
   * @param GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory[] $directMemories
   */
  public function setDirectMemories($directMemories)
  {
    $this->directMemories = $directMemories;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory[]
   */
  public function getDirectMemories()
  {
    return $this->directMemories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource');
