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

namespace Google\Service\Dataflow;

class AutoscalingSettings extends \Google\Model
{
  /**
   * The algorithm is unknown, or unspecified.
   */
  public const ALGORITHM_AUTOSCALING_ALGORITHM_UNKNOWN = 'AUTOSCALING_ALGORITHM_UNKNOWN';
  /**
   * Disable autoscaling.
   */
  public const ALGORITHM_AUTOSCALING_ALGORITHM_NONE = 'AUTOSCALING_ALGORITHM_NONE';
  /**
   * Increase worker count over time to reduce job execution time.
   */
  public const ALGORITHM_AUTOSCALING_ALGORITHM_BASIC = 'AUTOSCALING_ALGORITHM_BASIC';
  /**
   * The algorithm to use for autoscaling.
   *
   * @var string
   */
  public $algorithm;
  /**
   * The maximum number of workers to cap scaling at.
   *
   * @var int
   */
  public $maxNumWorkers;

  /**
   * The algorithm to use for autoscaling.
   *
   * Accepted values: AUTOSCALING_ALGORITHM_UNKNOWN, AUTOSCALING_ALGORITHM_NONE,
   * AUTOSCALING_ALGORITHM_BASIC
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * The maximum number of workers to cap scaling at.
   *
   * @param int $maxNumWorkers
   */
  public function setMaxNumWorkers($maxNumWorkers)
  {
    $this->maxNumWorkers = $maxNumWorkers;
  }
  /**
   * @return int
   */
  public function getMaxNumWorkers()
  {
    return $this->maxNumWorkers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingSettings::class, 'Google_Service_Dataflow_AutoscalingSettings');
