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

namespace Google\Service\FirebaseAppHosting;

class Traffic extends \Google\Model
{
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Time at which the backend was created.
   *
   * @var string
   */
  public $createTime;
  protected $currentType = TrafficSet::class;
  protected $currentDataType = '';
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the backend's traffic. Format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}/traffic`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that, if true, indicates that the system is working to
   * make the backend's `current` match the requested `target` list.
   *
   * @var bool
   */
  public $reconciling;
  protected $rolloutPolicyType = RolloutPolicy::class;
  protected $rolloutPolicyDataType = '';
  protected $targetType = TrafficSet::class;
  protected $targetDataType = '';
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the backend was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Time at which the backend was created.
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
   * Output only. Current state of traffic allocation for the backend. When
   * setting `target`, this field may differ for some time until the desired
   * state is reached.
   *
   * @param TrafficSet $current
   */
  public function setCurrent(TrafficSet $current)
  {
    $this->current = $current;
  }
  /**
   * @return TrafficSet
   */
  public function getCurrent()
  {
    return $this->current;
  }
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
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
   * Identifier. The resource name of the backend's traffic. Format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}/traffic`.
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
   * Output only. A field that, if true, indicates that the system is working to
   * make the backend's `current` match the requested `target` list.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * A rollout policy specifies how new builds and automatic deployments are
   * created.
   *
   * @param RolloutPolicy $rolloutPolicy
   */
  public function setRolloutPolicy(RolloutPolicy $rolloutPolicy)
  {
    $this->rolloutPolicy = $rolloutPolicy;
  }
  /**
   * @return RolloutPolicy
   */
  public function getRolloutPolicy()
  {
    return $this->rolloutPolicy;
  }
  /**
   * Set to manually control the desired traffic for the backend. This will
   * cause `current` to eventually match this value. The percentages must add up
   * to 100%.
   *
   * @param TrafficSet $target
   */
  public function setTarget(TrafficSet $target)
  {
    $this->target = $target;
  }
  /**
   * @return TrafficSet
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Time at which the backend was last updated.
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
class_alias(Traffic::class, 'Google_Service_FirebaseAppHosting_Traffic');
