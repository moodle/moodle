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

class GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory extends \Google\Collection
{
  protected $collection_key = 'topics';
  /**
   * Required. The fact to consolidate with existing memories.
   *
   * @var string
   */
  public $fact;
  protected $topicsType = GoogleCloudAiplatformV1MemoryTopicId::class;
  protected $topicsDataType = 'array';

  /**
   * Required. The fact to consolidate with existing memories.
   *
   * @param string $fact
   */
  public function setFact($fact)
  {
    $this->fact = $fact;
  }
  /**
   * @return string
   */
  public function getFact()
  {
    return $this->fact;
  }
  /**
   * Optional. The topics that the consolidated memories should be associated
   * with.
   *
   * @param GoogleCloudAiplatformV1MemoryTopicId[] $topics
   */
  public function setTopics($topics)
  {
    $this->topics = $topics;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryTopicId[]
   */
  public function getTopics()
  {
    return $this->topics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSourceDirectMemory');
