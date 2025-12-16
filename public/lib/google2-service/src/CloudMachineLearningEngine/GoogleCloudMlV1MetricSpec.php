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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1MetricSpec extends \Google\Model
{
  /**
   * Unspecified MetricName.
   */
  public const NAME_METRIC_NAME_UNSPECIFIED = 'METRIC_NAME_UNSPECIFIED';
  /**
   * CPU usage.
   */
  public const NAME_CPU_USAGE = 'CPU_USAGE';
  /**
   * GPU duty cycle.
   */
  public const NAME_GPU_DUTY_CYCLE = 'GPU_DUTY_CYCLE';
  /**
   * metric name.
   *
   * @var string
   */
  public $name;
  /**
   * Target specifies the target value for the given metric; once real metric
   * deviates from the threshold by a certain percentage, the node count
   * changes.
   *
   * @var int
   */
  public $target;

  /**
   * metric name.
   *
   * Accepted values: METRIC_NAME_UNSPECIFIED, CPU_USAGE, GPU_DUTY_CYCLE
   *
   * @param self::NAME_* $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return self::NAME_*
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Target specifies the target value for the given metric; once real metric
   * deviates from the threshold by a certain percentage, the node count
   * changes.
   *
   * @param int $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return int
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1MetricSpec::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1MetricSpec');
