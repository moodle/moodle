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

namespace Google\Service\Logging;

class SuppressionInfo extends \Google\Model
{
  /**
   * Unexpected default.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * Indicates suppression occurred due to relevant entries being received in
   * excess of rate limits. For quotas and limits, see Logging API quotas and
   * limits (https://cloud.google.com/logging/quotas#api-limits).
   */
  public const REASON_RATE_LIMIT = 'RATE_LIMIT';
  /**
   * Indicates suppression occurred due to the client not consuming responses
   * quickly enough.
   */
  public const REASON_NOT_CONSUMED = 'NOT_CONSUMED';
  /**
   * The reason that entries were omitted from the session.
   *
   * @var string
   */
  public $reason;
  /**
   * A lower bound on the count of entries omitted due to reason.
   *
   * @var int
   */
  public $suppressedCount;

  /**
   * The reason that entries were omitted from the session.
   *
   * Accepted values: REASON_UNSPECIFIED, RATE_LIMIT, NOT_CONSUMED
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * A lower bound on the count of entries omitted due to reason.
   *
   * @param int $suppressedCount
   */
  public function setSuppressedCount($suppressedCount)
  {
    $this->suppressedCount = $suppressedCount;
  }
  /**
   * @return int
   */
  public function getSuppressedCount()
  {
    return $this->suppressedCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuppressionInfo::class, 'Google_Service_Logging_SuppressionInfo');
