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

namespace Google\Service\BigQueryReservation;

class FailoverReservationRequest extends \Google\Model
{
  /**
   * Invalid value.
   */
  public const FAILOVER_MODE_FAILOVER_MODE_UNSPECIFIED = 'FAILOVER_MODE_UNSPECIFIED';
  /**
   * When customers initiate a soft failover, BigQuery will wait until all
   * committed writes are replicated to the secondary. This mode requires both
   * regions to be available for the failover to succeed and prevents data loss.
   */
  public const FAILOVER_MODE_SOFT = 'SOFT';
  /**
   * When customers initiate a hard failover, BigQuery will not wait until all
   * committed writes are replicated to the secondary. There can be data loss
   * for hard failover.
   */
  public const FAILOVER_MODE_HARD = 'HARD';
  /**
   * Optional. A parameter that determines how writes that are pending
   * replication are handled after a failover is initiated. If not specified,
   * HARD failover mode is used by default.
   *
   * @var string
   */
  public $failoverMode;

  /**
   * Optional. A parameter that determines how writes that are pending
   * replication are handled after a failover is initiated. If not specified,
   * HARD failover mode is used by default.
   *
   * Accepted values: FAILOVER_MODE_UNSPECIFIED, SOFT, HARD
   *
   * @param self::FAILOVER_MODE_* $failoverMode
   */
  public function setFailoverMode($failoverMode)
  {
    $this->failoverMode = $failoverMode;
  }
  /**
   * @return self::FAILOVER_MODE_*
   */
  public function getFailoverMode()
  {
    return $this->failoverMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FailoverReservationRequest::class, 'Google_Service_BigQueryReservation_FailoverReservationRequest');
