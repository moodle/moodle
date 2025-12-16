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

namespace Google\Service\SecureSourceManager;

class Hook extends \Google\Collection
{
  protected $collection_key = 'events';
  /**
   * Output only. Create timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Determines if the hook disabled or not. Set to true to stop
   * sending traffic.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. The events that trigger hook on.
   *
   * @var string[]
   */
  public $events;
  /**
   * Identifier. A unique identifier for a Hook. The name should be of the
   * format: `projects/{project}/locations/{location_id}/repositories/{repositor
   * y_id}/hooks/{hook_id}`
   *
   * @var string
   */
  public $name;
  protected $pushOptionType = PushOption::class;
  protected $pushOptionDataType = '';
  /**
   * Optional. The sensitive query string to be appended to the target URI.
   *
   * @var string
   */
  public $sensitiveQueryString;
  /**
   * Required. The target URI to which the payloads will be delivered.
   *
   * @var string
   */
  public $targetUri;
  /**
   * Output only. Unique identifier of the hook.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create timestamp.
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
   * Optional. Determines if the hook disabled or not. Set to true to stop
   * sending traffic.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. The events that trigger hook on.
   *
   * @param string[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return string[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * Identifier. A unique identifier for a Hook. The name should be of the
   * format: `projects/{project}/locations/{location_id}/repositories/{repositor
   * y_id}/hooks/{hook_id}`
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
   * Optional. The trigger option for push events.
   *
   * @param PushOption $pushOption
   */
  public function setPushOption(PushOption $pushOption)
  {
    $this->pushOption = $pushOption;
  }
  /**
   * @return PushOption
   */
  public function getPushOption()
  {
    return $this->pushOption;
  }
  /**
   * Optional. The sensitive query string to be appended to the target URI.
   *
   * @param string $sensitiveQueryString
   */
  public function setSensitiveQueryString($sensitiveQueryString)
  {
    $this->sensitiveQueryString = $sensitiveQueryString;
  }
  /**
   * @return string
   */
  public function getSensitiveQueryString()
  {
    return $this->sensitiveQueryString;
  }
  /**
   * Required. The target URI to which the payloads will be delivered.
   *
   * @param string $targetUri
   */
  public function setTargetUri($targetUri)
  {
    $this->targetUri = $targetUri;
  }
  /**
   * @return string
   */
  public function getTargetUri()
  {
    return $this->targetUri;
  }
  /**
   * Output only. Unique identifier of the hook.
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
   * Output only. Update timestamp.
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
class_alias(Hook::class, 'Google_Service_SecureSourceManager_Hook');
