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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1Period extends \Google\Model
{
  /**
   * Not used.
   */
  public const PERIOD_TYPE_PERIOD_TYPE_UNSPECIFIED = 'PERIOD_TYPE_UNSPECIFIED';
  /**
   * Day.
   */
  public const PERIOD_TYPE_DAY = 'DAY';
  /**
   * Month.
   */
  public const PERIOD_TYPE_MONTH = 'MONTH';
  /**
   * Year.
   */
  public const PERIOD_TYPE_YEAR = 'YEAR';
  /**
   * Total duration of Period Type defined.
   *
   * @var int
   */
  public $duration;
  /**
   * Period Type.
   *
   * @var string
   */
  public $periodType;

  /**
   * Total duration of Period Type defined.
   *
   * @param int $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return int
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Period Type.
   *
   * Accepted values: PERIOD_TYPE_UNSPECIFIED, DAY, MONTH, YEAR
   *
   * @param self::PERIOD_TYPE_* $periodType
   */
  public function setPeriodType($periodType)
  {
    $this->periodType = $periodType;
  }
  /**
   * @return self::PERIOD_TYPE_*
   */
  public function getPeriodType()
  {
    return $this->periodType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1Period::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1Period');
