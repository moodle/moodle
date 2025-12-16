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

namespace Google\Service\CloudAlloyDBAdmin;

class QuantityBasedExpiry extends \Google\Model
{
  /**
   * Output only. The backup's position among its backups with the same source
   * cluster and type, by descending chronological order create time(i.e. newest
   * first).
   *
   * @var int
   */
  public $retentionCount;
  /**
   * Output only. The length of the quantity-based queue, specified by the
   * backup's retention policy.
   *
   * @var int
   */
  public $totalRetentionCount;

  /**
   * Output only. The backup's position among its backups with the same source
   * cluster and type, by descending chronological order create time(i.e. newest
   * first).
   *
   * @param int $retentionCount
   */
  public function setRetentionCount($retentionCount)
  {
    $this->retentionCount = $retentionCount;
  }
  /**
   * @return int
   */
  public function getRetentionCount()
  {
    return $this->retentionCount;
  }
  /**
   * Output only. The length of the quantity-based queue, specified by the
   * backup's retention policy.
   *
   * @param int $totalRetentionCount
   */
  public function setTotalRetentionCount($totalRetentionCount)
  {
    $this->totalRetentionCount = $totalRetentionCount;
  }
  /**
   * @return int
   */
  public function getTotalRetentionCount()
  {
    return $this->totalRetentionCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuantityBasedExpiry::class, 'Google_Service_CloudAlloyDBAdmin_QuantityBasedExpiry');
