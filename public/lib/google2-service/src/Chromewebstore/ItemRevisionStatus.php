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

namespace Google\Service\Chromewebstore;

class ItemRevisionStatus extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_ITEM_STATE_UNSPECIFIED = 'ITEM_STATE_UNSPECIFIED';
  /**
   * The item is pending review.
   */
  public const STATE_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * The item has been approved and is ready to be published.
   */
  public const STATE_STAGED = 'STAGED';
  /**
   * The item is published publicly.
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  /**
   * The item is published to trusted testers.
   */
  public const STATE_PUBLISHED_TO_TESTERS = 'PUBLISHED_TO_TESTERS';
  /**
   * The item has been rejected for publishing.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * The item submission has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $collection_key = 'distributionChannels';
  protected $distributionChannelsType = DistributionChannel::class;
  protected $distributionChannelsDataType = 'array';
  /**
   * Output only. Current state of the item
   *
   * @var string
   */
  public $state;

  /**
   * Details on the package of the item
   *
   * @param DistributionChannel[] $distributionChannels
   */
  public function setDistributionChannels($distributionChannels)
  {
    $this->distributionChannels = $distributionChannels;
  }
  /**
   * @return DistributionChannel[]
   */
  public function getDistributionChannels()
  {
    return $this->distributionChannels;
  }
  /**
   * Output only. Current state of the item
   *
   * Accepted values: ITEM_STATE_UNSPECIFIED, PENDING_REVIEW, STAGED, PUBLISHED,
   * PUBLISHED_TO_TESTERS, REJECTED, CANCELLED
   *
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
class_alias(ItemRevisionStatus::class, 'Google_Service_Chromewebstore_ItemRevisionStatus');
