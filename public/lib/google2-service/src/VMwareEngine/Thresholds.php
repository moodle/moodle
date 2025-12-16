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

namespace Google\Service\VMwareEngine;

class Thresholds extends \Google\Model
{
  /**
   * Required. The utilization triggering the scale-in operation in percent.
   *
   * @var int
   */
  public $scaleIn;
  /**
   * Required. The utilization triggering the scale-out operation in percent.
   *
   * @var int
   */
  public $scaleOut;

  /**
   * Required. The utilization triggering the scale-in operation in percent.
   *
   * @param int $scaleIn
   */
  public function setScaleIn($scaleIn)
  {
    $this->scaleIn = $scaleIn;
  }
  /**
   * @return int
   */
  public function getScaleIn()
  {
    return $this->scaleIn;
  }
  /**
   * Required. The utilization triggering the scale-out operation in percent.
   *
   * @param int $scaleOut
   */
  public function setScaleOut($scaleOut)
  {
    $this->scaleOut = $scaleOut;
  }
  /**
   * @return int
   */
  public function getScaleOut()
  {
    return $this->scaleOut;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Thresholds::class, 'Google_Service_VMwareEngine_Thresholds');
