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

namespace Google\Service\CloudDeploy;

class TimedPromoteReleaseCondition extends \Google\Collection
{
  protected $collection_key = 'targetsList';
  /**
   * Output only. When the next scheduled promotion(s) will occur.
   *
   * @var string
   */
  public $nextPromotionTime;
  protected $targetsListType = Targets::class;
  protected $targetsListDataType = 'array';

  /**
   * Output only. When the next scheduled promotion(s) will occur.
   *
   * @param string $nextPromotionTime
   */
  public function setNextPromotionTime($nextPromotionTime)
  {
    $this->nextPromotionTime = $nextPromotionTime;
  }
  /**
   * @return string
   */
  public function getNextPromotionTime()
  {
    return $this->nextPromotionTime;
  }
  /**
   * Output only. A list of targets involved in the upcoming timed promotion(s).
   *
   * @param Targets[] $targetsList
   */
  public function setTargetsList($targetsList)
  {
    $this->targetsList = $targetsList;
  }
  /**
   * @return Targets[]
   */
  public function getTargetsList()
  {
    return $this->targetsList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimedPromoteReleaseCondition::class, 'Google_Service_CloudDeploy_TimedPromoteReleaseCondition');
