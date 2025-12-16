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

namespace Google\Service\ShoppingContent;

class PromotionPromotionStatusDestinationStatus extends \Google\Model
{
  /**
   * Unknown promotion state.
   */
  public const STATUS_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The promotion is under review.
   */
  public const STATUS_IN_REVIEW = 'IN_REVIEW';
  /**
   * The promotion is disapproved
   */
  public const STATUS_REJECTED = 'REJECTED';
  /**
   * The promotion is approved and active.
   */
  public const STATUS_LIVE = 'LIVE';
  /**
   * The promotion is stopped by merchant.
   */
  public const STATUS_STOPPED = 'STOPPED';
  /**
   * The promotion is no longer active.
   */
  public const STATUS_EXPIRED = 'EXPIRED';
  /**
   * The promotion is not stopped, and all reviews are approved, but the active
   * date is in the future.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The name of the destination.
   *
   * @var string
   */
  public $destination;
  /**
   * The status for the specified destination.
   *
   * @var string
   */
  public $status;

  /**
   * The name of the destination.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * The status for the specified destination.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_REVIEW, REJECTED, LIVE, STOPPED,
   * EXPIRED, PENDING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PromotionPromotionStatusDestinationStatus::class, 'Google_Service_ShoppingContent_PromotionPromotionStatusDestinationStatus');
