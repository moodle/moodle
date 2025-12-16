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

namespace Google\Service\ManagedKafka;

class RebalanceConfig extends \Google\Model
{
  /**
   * A mode was not specified. Do not use.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Do not rebalance automatically.
   */
  public const MODE_NO_REBALANCE = 'NO_REBALANCE';
  /**
   * Automatically rebalance topic partitions among brokers when the cluster is
   * scaled up.
   */
  public const MODE_AUTO_REBALANCE_ON_SCALE_UP = 'AUTO_REBALANCE_ON_SCALE_UP';
  /**
   * Optional. The rebalance behavior for the cluster. When not specified,
   * defaults to `NO_REBALANCE`.
   *
   * @var string
   */
  public $mode;

  /**
   * Optional. The rebalance behavior for the cluster. When not specified,
   * defaults to `NO_REBALANCE`.
   *
   * Accepted values: MODE_UNSPECIFIED, NO_REBALANCE, AUTO_REBALANCE_ON_SCALE_UP
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RebalanceConfig::class, 'Google_Service_ManagedKafka_RebalanceConfig');
