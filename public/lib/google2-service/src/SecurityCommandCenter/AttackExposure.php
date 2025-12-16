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

namespace Google\Service\SecurityCommandCenter;

class AttackExposure extends \Google\Model
{
  /**
   * The state is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The attack exposure has been calculated.
   */
  public const STATE_CALCULATED = 'CALCULATED';
  /**
   * The attack exposure has not been calculated.
   */
  public const STATE_NOT_CALCULATED = 'NOT_CALCULATED';
  /**
   * The resource name of the attack path simulation result that contains the
   * details regarding this attack exposure score. Example:
   * `organizations/123/simulations/456/attackExposureResults/789`
   *
   * @var string
   */
  public $attackExposureResult;
  /**
   * The number of high value resources that are exposed as a result of this
   * finding.
   *
   * @var int
   */
  public $exposedHighValueResourcesCount;
  /**
   * The number of high value resources that are exposed as a result of this
   * finding.
   *
   * @var int
   */
  public $exposedLowValueResourcesCount;
  /**
   * The number of medium value resources that are exposed as a result of this
   * finding.
   *
   * @var int
   */
  public $exposedMediumValueResourcesCount;
  /**
   * The most recent time the attack exposure was updated on this finding.
   *
   * @var string
   */
  public $latestCalculationTime;
  /**
   * A number between 0 (inclusive) and infinity that represents how important
   * this finding is to remediate. The higher the score, the more important it
   * is to remediate.
   *
   * @var 
   */
  public $score;
  /**
   * What state this AttackExposure is in. This captures whether or not an
   * attack exposure has been calculated or not.
   *
   * @var string
   */
  public $state;

  /**
   * The resource name of the attack path simulation result that contains the
   * details regarding this attack exposure score. Example:
   * `organizations/123/simulations/456/attackExposureResults/789`
   *
   * @param string $attackExposureResult
   */
  public function setAttackExposureResult($attackExposureResult)
  {
    $this->attackExposureResult = $attackExposureResult;
  }
  /**
   * @return string
   */
  public function getAttackExposureResult()
  {
    return $this->attackExposureResult;
  }
  /**
   * The number of high value resources that are exposed as a result of this
   * finding.
   *
   * @param int $exposedHighValueResourcesCount
   */
  public function setExposedHighValueResourcesCount($exposedHighValueResourcesCount)
  {
    $this->exposedHighValueResourcesCount = $exposedHighValueResourcesCount;
  }
  /**
   * @return int
   */
  public function getExposedHighValueResourcesCount()
  {
    return $this->exposedHighValueResourcesCount;
  }
  /**
   * The number of high value resources that are exposed as a result of this
   * finding.
   *
   * @param int $exposedLowValueResourcesCount
   */
  public function setExposedLowValueResourcesCount($exposedLowValueResourcesCount)
  {
    $this->exposedLowValueResourcesCount = $exposedLowValueResourcesCount;
  }
  /**
   * @return int
   */
  public function getExposedLowValueResourcesCount()
  {
    return $this->exposedLowValueResourcesCount;
  }
  /**
   * The number of medium value resources that are exposed as a result of this
   * finding.
   *
   * @param int $exposedMediumValueResourcesCount
   */
  public function setExposedMediumValueResourcesCount($exposedMediumValueResourcesCount)
  {
    $this->exposedMediumValueResourcesCount = $exposedMediumValueResourcesCount;
  }
  /**
   * @return int
   */
  public function getExposedMediumValueResourcesCount()
  {
    return $this->exposedMediumValueResourcesCount;
  }
  /**
   * The most recent time the attack exposure was updated on this finding.
   *
   * @param string $latestCalculationTime
   */
  public function setLatestCalculationTime($latestCalculationTime)
  {
    $this->latestCalculationTime = $latestCalculationTime;
  }
  /**
   * @return string
   */
  public function getLatestCalculationTime()
  {
    return $this->latestCalculationTime;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * What state this AttackExposure is in. This captures whether or not an
   * attack exposure has been calculated or not.
   *
   * Accepted values: STATE_UNSPECIFIED, CALCULATED, NOT_CALCULATED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttackExposure::class, 'Google_Service_SecurityCommandCenter_AttackExposure');
