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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Session extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is active, i.e., ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource is under creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is under deletion.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Resource is active but has unresolved actions.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * Output only. Session start time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The relative resource name of the content, of the form: projec
   * ts/{project_id}/locations/{location_id}/lakes/{lake_id}/environment/{enviro
   * nment_id}/sessions/{session_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of Session
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Email of user running the session.
   *
   * @var string
   */
  public $userId;

  /**
   * Output only. Session start time.
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
   * Output only. The relative resource name of the content, of the form: projec
   * ts/{project_id}/locations/{location_id}/lakes/{lake_id}/environment/{enviro
   * nment_id}/sessions/{session_id}
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
   * Output only. State of Session
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * ACTION_REQUIRED
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
   * Output only. Email of user running the session.
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
class_alias(GoogleCloudDataplexV1Session::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Session');
