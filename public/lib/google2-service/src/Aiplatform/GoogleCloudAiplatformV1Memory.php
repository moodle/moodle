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

class GoogleCloudAiplatformV1Memory extends \Google\Collection
{
  protected $collection_key = 'topics';
  /**
   * Output only. Timestamp when this Memory was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the Memory.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Input only. If true, no revision will be created for this
   * request.
   *
   * @var bool
   */
  public $disableMemoryRevisions;
  /**
   * Optional. Display name of the Memory.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Timestamp of when this resource is considered expired. This is
   * *always* provided on output when `expiration` is set on input, regardless
   * of whether `expire_time` or `ttl` was provided.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Required. Semantic knowledge extracted from the source content.
   *
   * @var string
   */
  public $fact;
  /**
   * Identifier. The resource name of the Memory. Format: `projects/{project}/lo
   * cations/{location}/reasoningEngines/{reasoning_engine}/memories/{memory}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Input only. Timestamp of when the revision is considered expired.
   * If not set, the memory revision will be kept until manually deleted.
   *
   * @var string
   */
  public $revisionExpireTime;
  /**
   * Optional. Input only. The labels to apply to the Memory Revision created as
   * a result of this request.
   *
   * @var string[]
   */
  public $revisionLabels;
  /**
   * Optional. Input only. The TTL for the revision. The expiration time is
   * computed: now + TTL.
   *
   * @var string
   */
  public $revisionTtl;
  /**
   * Required. Immutable. The scope of the Memory. Memories are isolated within
   * their scope. The scope is defined when creating or generating memories.
   * Scope values cannot contain the wildcard character '*'.
   *
   * @var string[]
   */
  public $scope;
  protected $topicsType = GoogleCloudAiplatformV1MemoryTopicId::class;
  protected $topicsDataType = 'array';
  /**
   * Optional. Input only. The TTL for this resource. The expiration time is
   * computed: now + TTL.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. Timestamp when this Memory was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this Memory was created.
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
   * Optional. Description of the Memory.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Input only. If true, no revision will be created for this
   * request.
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
   * Optional. Display name of the Memory.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Timestamp of when this resource is considered expired. This is
   * *always* provided on output when `expiration` is set on input, regardless
   * of whether `expire_time` or `ttl` was provided.
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
   * Required. Semantic knowledge extracted from the source content.
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
   * Identifier. The resource name of the Memory. Format: `projects/{project}/lo
   * cations/{location}/reasoningEngines/{reasoning_engine}/memories/{memory}`
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
   * Optional. Input only. Timestamp of when the revision is considered expired.
   * If not set, the memory revision will be kept until manually deleted.
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
   * Optional. Input only. The labels to apply to the Memory Revision created as
   * a result of this request.
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
   * Optional. Input only. The TTL for the revision. The expiration time is
   * computed: now + TTL.
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
   * Required. Immutable. The scope of the Memory. Memories are isolated within
   * their scope. The scope is defined when creating or generating memories.
   * Scope values cannot contain the wildcard character '*'.
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
   * Optional. The Topics of the Memory.
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
  /**
   * Optional. Input only. The TTL for this resource. The expiration time is
   * computed: now + TTL.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. Timestamp when this Memory was most recently updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Memory::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Memory');
