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

namespace Google\Service\CloudFilestore;

class Snapshot extends \Google\Model
{
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Snapshot is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Snapshot is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * Snapshot is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. The time when the snapshot was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the snapshot with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The amount of bytes needed to allocate a full copy of the
   * snapshot content
   *
   * @var string
   */
  public $filesystemUsedBytes;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the snapshot, in the format `projects/{pr
   * oject_id}/locations/{location_id}/instances/{instance_id}/snapshots/{snapsh
   * ot_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The snapshot state.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
   *
   * @var string[]
   */
  public $tags;

  /**
   * Output only. The time when the snapshot was created.
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
   * A description of the snapshot with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
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
   * Output only. The amount of bytes needed to allocate a full copy of the
   * snapshot content
   *
   * @param string $filesystemUsedBytes
   */
  public function setFilesystemUsedBytes($filesystemUsedBytes)
  {
    $this->filesystemUsedBytes = $filesystemUsedBytes;
  }
  /**
   * @return string
   */
  public function getFilesystemUsedBytes()
  {
    return $this->filesystemUsedBytes;
  }
  /**
   * Resource labels to represent user provided metadata.
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
   * Output only. The resource name of the snapshot, in the format `projects/{pr
   * oject_id}/locations/{location_id}/instances/{instance_id}/snapshots/{snapsh
   * ot_id}`.
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
   * Output only. The snapshot state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Snapshot::class, 'Google_Service_CloudFilestore_Snapshot');
