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

class GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory extends \Google\Model
{
  /**
   * The distance between the query and the retrieved Memory. Smaller values
   * indicate more similar memories. This is only set if similarity search was
   * used for retrieval.
   *
   * @var 
   */
  public $distance;
  protected $memoryType = GoogleCloudAiplatformV1Memory::class;
  protected $memoryDataType = '';

  public function setDistance($distance)
  {
    $this->distance = $distance;
  }
  public function getDistance()
  {
    return $this->distance;
  }
  /**
   * The retrieved Memory.
   *
   * @param GoogleCloudAiplatformV1Memory $memory
   */
  public function setMemory(GoogleCloudAiplatformV1Memory $memory)
  {
    $this->memory = $memory;
  }
  /**
   * @return GoogleCloudAiplatformV1Memory
   */
  public function getMemory()
  {
    return $this->memory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RetrieveMemoriesResponseRetrievedMemory');
