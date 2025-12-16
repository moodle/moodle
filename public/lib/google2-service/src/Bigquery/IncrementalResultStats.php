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

namespace Google\Service\Bigquery;

class IncrementalResultStats extends \Google\Model
{
  /**
   * Disabled reason not specified.
   */
  public const DISABLED_REASON_DISABLED_REASON_UNSPECIFIED = 'DISABLED_REASON_UNSPECIFIED';
  /**
   * Some other reason.
   */
  public const DISABLED_REASON_OTHER = 'OTHER';
  /**
   * Reason why incremental query results are/were not written by the query.
   *
   * @var string
   */
  public $disabledReason;
  /**
   * The time at which the result table's contents were modified. May be absent
   * if no results have been written or the query has completed.
   *
   * @var string
   */
  public $resultSetLastModifyTime;
  /**
   * The time at which the result table's contents were completely replaced. May
   * be absent if no results have been written or the query has completed.
   *
   * @var string
   */
  public $resultSetLastReplaceTime;

  /**
   * Reason why incremental query results are/were not written by the query.
   *
   * Accepted values: DISABLED_REASON_UNSPECIFIED, OTHER
   *
   * @param self::DISABLED_REASON_* $disabledReason
   */
  public function setDisabledReason($disabledReason)
  {
    $this->disabledReason = $disabledReason;
  }
  /**
   * @return self::DISABLED_REASON_*
   */
  public function getDisabledReason()
  {
    return $this->disabledReason;
  }
  /**
   * The time at which the result table's contents were modified. May be absent
   * if no results have been written or the query has completed.
   *
   * @param string $resultSetLastModifyTime
   */
  public function setResultSetLastModifyTime($resultSetLastModifyTime)
  {
    $this->resultSetLastModifyTime = $resultSetLastModifyTime;
  }
  /**
   * @return string
   */
  public function getResultSetLastModifyTime()
  {
    return $this->resultSetLastModifyTime;
  }
  /**
   * The time at which the result table's contents were completely replaced. May
   * be absent if no results have been written or the query has completed.
   *
   * @param string $resultSetLastReplaceTime
   */
  public function setResultSetLastReplaceTime($resultSetLastReplaceTime)
  {
    $this->resultSetLastReplaceTime = $resultSetLastReplaceTime;
  }
  /**
   * @return string
   */
  public function getResultSetLastReplaceTime()
  {
    return $this->resultSetLastReplaceTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IncrementalResultStats::class, 'Google_Service_Bigquery_IncrementalResultStats');
