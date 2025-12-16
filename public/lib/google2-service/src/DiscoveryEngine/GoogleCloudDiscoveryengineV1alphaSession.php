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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaSession extends \Google\Collection
{
  /**
   * State is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The session is currently open.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  protected $collection_key = 'turns';
  /**
   * Optional. The display name of the session. This field is used to identify
   * the session in the UI. By default, the display name is the first turn query
   * text in the session.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The time the session finished.
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. Whether the session is pinned, pinned session will be displayed
   * on the top of the session list.
   *
   * @var bool
   */
  public $isPinned;
  /**
   * Optional. The labels for the session. Can be set as filter in
   * ListSessionsRequest.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/engines/{engine}/sessions`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time the session started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The state of the session.
   *
   * @var string
   */
  public $state;
  protected $turnsType = GoogleCloudDiscoveryengineV1alphaSessionTurn::class;
  protected $turnsDataType = 'array';
  /**
   * A unique identifier for tracking users.
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Optional. The display name of the session. This field is used to identify
   * the session in the UI. By default, the display name is the first turn query
   * text in the session.
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
   * Output only. The time the session finished.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. Whether the session is pinned, pinned session will be displayed
   * on the top of the session list.
   *
   * @param bool $isPinned
   */
  public function setIsPinned($isPinned)
  {
    $this->isPinned = $isPinned;
  }
  /**
   * @return bool
   */
  public function getIsPinned()
  {
    return $this->isPinned;
  }
  /**
   * Optional. The labels for the session. Can be set as filter in
   * ListSessionsRequest.
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
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/engines/{engine}/sessions`
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
   * Output only. The time the session started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The state of the session.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS
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
   * Turns.
   *
   * @param GoogleCloudDiscoveryengineV1alphaSessionTurn[] $turns
   */
  public function setTurns($turns)
  {
    $this->turns = $turns;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSessionTurn[]
   */
  public function getTurns()
  {
    return $this->turns;
  }
  /**
   * A unique identifier for tracking users.
   *
   * @param string $userPseudoId
   */
  public function setUserPseudoId($userPseudoId)
  {
    $this->userPseudoId = $userPseudoId;
  }
  /**
   * @return string
   */
  public function getUserPseudoId()
  {
    return $this->userPseudoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSession::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSession');
