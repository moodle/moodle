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

class GoogleCloudAiplatformV1GenerateMemoriesRequest extends \Google\Model
{
  protected $directContentsSourceType = GoogleCloudAiplatformV1GenerateMemoriesRequestDirectContentsSource::class;
  protected $directContentsSourceDataType = '';
  protected $directMemoriesSourceType = GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource::class;
  protected $directMemoriesSourceDataType = '';
  /**
   * Optional. If true, generated memories will not be consolidated with
   * existing memories; all generated memories will be added as new memories
   * regardless of whether they are duplicates of or contradictory to existing
   * memories. By default, memory consolidation is enabled.
   *
   * @var bool
   */
  public $disableConsolidation;
  /**
   * Optional. If true, no revisions will be created for this request.
   *
   * @var bool
   */
  public $disableMemoryRevisions;
  /**
   * Optional. Timestamp of when the revision is considered expired. If not set,
   * the memory revision will be kept until manually deleted.
   *
   * @var string
   */
  public $revisionExpireTime;
  /**
   * Optional. Labels to be applied to the generated memory revisions. For
   * example, you can use this to label a revision with its data source.
   *
   * @var string[]
   */
  public $revisionLabels;
  /**
   * Optional. The TTL for the revision. The expiration time is computed: now +
   * TTL.
   *
   * @var string
   */
  public $revisionTtl;
  /**
   * Optional. The scope of the memories that should be generated. Memories will
   * be consolidated across memories with the same scope. Must be provided
   * unless the scope is defined in the source content. If `scope` is provided,
   * it will override the scope defined in the source content. Scope values
   * cannot contain the wildcard character '*'.
   *
   * @var string[]
   */
  public $scope;
  protected $vertexSessionSourceType = GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource::class;
  protected $vertexSessionSourceDataType = '';

  /**
   * Defines a direct source of content as the source content from which to
   * generate memories.
   *
   * @param GoogleCloudAiplatformV1GenerateMemoriesRequestDirectContentsSource $directContentsSource
   */
  public function setDirectContentsSource(GoogleCloudAiplatformV1GenerateMemoriesRequestDirectContentsSource $directContentsSource)
  {
    $this->directContentsSource = $directContentsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateMemoriesRequestDirectContentsSource
   */
  public function getDirectContentsSource()
  {
    return $this->directContentsSource;
  }
  /**
   * Defines a direct source of memories that should be uploaded to Memory Bank.
   * This is similar to `CreateMemory`, but it allows for consolidation between
   * these new memories and existing memories for the same scope.
   *
   * @param GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource $directMemoriesSource
   */
  public function setDirectMemoriesSource(GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource $directMemoriesSource)
  {
    $this->directMemoriesSource = $directMemoriesSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateMemoriesRequestDirectMemoriesSource
   */
  public function getDirectMemoriesSource()
  {
    return $this->directMemoriesSource;
  }
  /**
   * Optional. If true, generated memories will not be consolidated with
   * existing memories; all generated memories will be added as new memories
   * regardless of whether they are duplicates of or contradictory to existing
   * memories. By default, memory consolidation is enabled.
   *
   * @param bool $disableConsolidation
   */
  public function setDisableConsolidation($disableConsolidation)
  {
    $this->disableConsolidation = $disableConsolidation;
  }
  /**
   * @return bool
   */
  public function getDisableConsolidation()
  {
    return $this->disableConsolidation;
  }
  /**
   * Optional. If true, no revisions will be created for this request.
   *
   * @param bool $disableMemoryRevisions
   */
  public function setDisableMemoryRevisions($disableMemoryRevisions)
  {
    $this->disableMemoryRevisions = $disableMemoryRevisions;
  }
  /**
   * @return bool
   */
  public function getDisableMemoryRevisions()
  {
    return $this->disableMemoryRevisions;
  }
  /**
   * Optional. Timestamp of when the revision is considered expired. If not set,
   * the memory revision will be kept until manually deleted.
   *
   * @param string $revisionExpireTime
   */
  public function setRevisionExpireTime($revisionExpireTime)
  {
    $this->revisionExpireTime = $revisionExpireTime;
  }
  /**
   * @return string
   */
  public function getRevisionExpireTime()
  {
    return $this->revisionExpireTime;
  }
  /**
   * Optional. Labels to be applied to the generated memory revisions. For
   * example, you can use this to label a revision with its data source.
   *
   * @param string[] $revisionLabels
   */
  public function setRevisionLabels($revisionLabels)
  {
    $this->revisionLabels = $revisionLabels;
  }
  /**
   * @return string[]
   */
  public function getRevisionLabels()
  {
    return $this->revisionLabels;
  }
  /**
   * Optional. The TTL for the revision. The expiration time is computed: now +
   * TTL.
   *
   * @param string $revisionTtl
   */
  public function setRevisionTtl($revisionTtl)
  {
    $this->revisionTtl = $revisionTtl;
  }
  /**
   * @return string
   */
  public function getRevisionTtl()
  {
    return $this->revisionTtl;
  }
  /**
   * Optional. The scope of the memories that should be generated. Memories will
   * be consolidated across memories with the same scope. Must be provided
   * unless the scope is defined in the source content. If `scope` is provided,
   * it will override the scope defined in the source content. Scope values
   * cannot contain the wildcard character '*'.
   *
   * @param string[] $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string[]
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Defines a Vertex Session as the source content from which to generate
   * memories.
   *
   * @param GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource $vertexSessionSource
   */
  public function setVertexSessionSource(GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource $vertexSessionSource)
  {
    $this->vertexSessionSource = $vertexSessionSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateMemoriesRequestVertexSessionSource
   */
  public function getVertexSessionSource()
  {
    return $this->vertexSessionSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateMemoriesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateMemoriesRequest');
