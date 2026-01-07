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

namespace Google\Service\APIManagement;

class ObservationSource extends \Google\Model
{
  /**
   * Unspecified state
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Source is in the creating state
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Source has been created and is ready to use
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * Source is being deleted
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Source is in an error state
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $createTime;
  protected $gclbObservationSourceType = GclbObservationSource::class;
  protected $gclbObservationSourceDataType = '';
  /**
   * Identifier. name of resource For MVP, each region can only have 1 source.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The observation source state
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output only] Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. [Output only] Create time stamp
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
   * The GCLB observation source
   *
   * @param GclbObservationSource $gclbObservationSource
   */
  public function setGclbObservationSource(GclbObservationSource $gclbObservationSource)
  {
    $this->gclbObservationSource = $gclbObservationSource;
  }
  /**
   * @return GclbObservationSource
   */
  public function getGclbObservationSource()
  {
    return $this->gclbObservationSource;
  }
  /**
   * Identifier. name of resource For MVP, each region can only have 1 source.
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
   * Output only. The observation source state
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, CREATED, DELETING, ERROR
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
   * Output only. [Output only] Update time stamp
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
class_alias(ObservationSource::class, 'Google_Service_APIManagement_ObservationSource');
