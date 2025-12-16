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

class GoogleCloudAiplatformV1MemoryRevision extends \Google\Collection
{
  protected $collection_key = 'extractedMemories';
  /**
   * Output only. Timestamp when this Memory Revision was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Timestamp of when this resource is considered expired.
   *
   * @var string
   */
  public $expireTime;
  protected $extractedMemoriesType = GoogleCloudAiplatformV1IntermediateExtractedMemory::class;
  protected $extractedMemoriesDataType = 'array';
  /**
   * Output only. The fact of the Memory Revision. This corresponds to the
   * `fact` field of the parent Memory at the time of revision creation.
   *
   * @var string
   */
  public $fact;
  /**
   * Output only. The labels of the Memory Revision. These labels are applied to
   * the MemoryRevision when it is created based on
   * `GenerateMemoriesRequest.revision_labels`.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the Memory Revision. Format: `projects/{pr
   * oject}/locations/{location}/reasoningEngines/{reasoning_engine}/memories/{m
   * emory}/revisions/{memory_revision}`
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Timestamp when this Memory Revision was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Timestamp of when this resource is considered expired.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The extracted memories from the source content before
   * consolidation when the memory was updated via GenerateMemories. This
   * information was used to modify an existing Memory via Consolidation.
   *
   * @param GoogleCloudAiplatformV1IntermediateExtractedMemory[] $extractedMemories
   */
  public function setExtractedMemories($extractedMemories)
  {
    $this->extractedMemories = $extractedMemories;
  }
  /**
   * @return GoogleCloudAiplatformV1IntermediateExtractedMemory[]
   */
  public function getExtractedMemories()
  {
    return $this->extractedMemories;
  }
  /**
   * Output only. The fact of the Memory Revision. This corresponds to the
   * `fact` field of the parent Memory at the time of revision creation.
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
   * Output only. The labels of the Memory Revision. These labels are applied to
   * the MemoryRevision when it is created based on
   * `GenerateMemoriesRequest.revision_labels`.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the Memory Revision. Format: `projects/{pr
   * oject}/locations/{location}/reasoningEngines/{reasoning_engine}/memories/{m
   * emory}/revisions/{memory_revision}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MemoryRevision::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MemoryRevision');
