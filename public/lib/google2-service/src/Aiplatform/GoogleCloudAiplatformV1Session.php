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

class GoogleCloudAiplatformV1Session extends \Google\Model
{
  /**
   * Output only. Timestamp when the session was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The display name of the session.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Timestamp of when this session is considered expired. This is
   * *always* provided on output, regardless of what was sent on input. The
   * minimum value is 24 hours from the time of creation.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The labels with user-defined metadata to organize your Sessions. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the session. Format: 'projects/{project}/l
   * ocations/{location}/reasoningEngines/{reasoning_engine}/sessions/{session}'
   * .
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Session specific memory which stores key conversation points.
   *
   * @var array[]
   */
  public $sessionState;
  /**
   * Optional. Input only. The TTL for this session. The minimum value is 24
   * hours.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. Timestamp when the session was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. Immutable. String id provided by the user
   *
   * @var string
   */
  public $userId;

  /**
   * Output only. Timestamp when the session was created.
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
   * Optional. The display name of the session.
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
   * Optional. Timestamp of when this session is considered expired. This is
   * *always* provided on output, regardless of what was sent on input. The
   * minimum value is 24 hours from the time of creation.
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
   * The labels with user-defined metadata to organize your Sessions. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
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
   * Identifier. The resource name of the session. Format: 'projects/{project}/l
   * ocations/{location}/reasoningEngines/{reasoning_engine}/sessions/{session}'
   * .
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
   * Optional. Session specific memory which stores key conversation points.
   *
   * @param array[] $sessionState
   */
  public function setSessionState($sessionState)
  {
    $this->sessionState = $sessionState;
  }
  /**
   * @return array[]
   */
  public function getSessionState()
  {
    return $this->sessionState;
  }
  /**
   * Optional. Input only. The TTL for this session. The minimum value is 24
   * hours.
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
   * Output only. Timestamp when the session was updated.
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
  /**
   * Required. Immutable. String id provided by the user
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Session::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Session');
