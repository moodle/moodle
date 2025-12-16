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

namespace Google\Service\BigtableAdmin;

class TableProgress extends \Google\Model
{
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The table has not yet begun copying to the new cluster.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The table is actively being copied to the new cluster.
   */
  public const STATE_COPYING = 'COPYING';
  /**
   * The table has been fully copied to the new cluster.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The table was deleted before it finished copying to the new cluster. Note
   * that tables deleted after completion will stay marked as COMPLETED, not
   * CANCELLED.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Estimate of the number of bytes copied so far for this table. This will
   * eventually reach 'estimated_size_bytes' unless the table copy is CANCELLED.
   *
   * @var string
   */
  public $estimatedCopiedBytes;
  /**
   * Estimate of the size of the table to be copied.
   *
   * @var string
   */
  public $estimatedSizeBytes;
  /**
   * @var string
   */
  public $state;

  /**
   * Estimate of the number of bytes copied so far for this table. This will
   * eventually reach 'estimated_size_bytes' unless the table copy is CANCELLED.
   *
   * @param string $estimatedCopiedBytes
   */
  public function setEstimatedCopiedBytes($estimatedCopiedBytes)
  {
    $this->estimatedCopiedBytes = $estimatedCopiedBytes;
  }
  /**
   * @return string
   */
  public function getEstimatedCopiedBytes()
  {
    return $this->estimatedCopiedBytes;
  }
  /**
   * Estimate of the size of the table to be copied.
   *
   * @param string $estimatedSizeBytes
   */
  public function setEstimatedSizeBytes($estimatedSizeBytes)
  {
    $this->estimatedSizeBytes = $estimatedSizeBytes;
  }
  /**
   * @return string
   */
  public function getEstimatedSizeBytes()
  {
    return $this->estimatedSizeBytes;
  }
  /**
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableProgress::class, 'Google_Service_BigtableAdmin_TableProgress');
