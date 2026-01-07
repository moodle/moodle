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

namespace Google\Service\DataprocMetastore;

class LimitConfig extends \Google\Model
{
  /**
   * @var float
   */
  public $maxScalingFactor;
  /**
   * @var float
   */
  public $minScalingFactor;

  /**
   * @param float
   */
  public function setMaxScalingFactor($maxScalingFactor)
  {
    $this->maxScalingFactor = $maxScalingFactor;
  }
  /**
   * @return float
   */
  public function getMaxScalingFactor()
  {
    return $this->maxScalingFactor;
  }
  /**
   * @param float
   */
  public function setMinScalingFactor($minScalingFactor)
  {
    $this->minScalingFactor = $minScalingFactor;
  }
  /**
   * @return float
   */
  public function getMinScalingFactor()
  {
    return $this->minScalingFactor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LimitConfig::class, 'Google_Service_DataprocMetastore_LimitConfig');
