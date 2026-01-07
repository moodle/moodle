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

class PublishItemResponse extends \Google\Model
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
  /**
   * Output only. The ID of the item.
   *
   * @var string
   */
  public $itemId;
  /**
   * The name of the item that was submitted
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the submission.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The ID of the item.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * The name of the item that was submitted
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The current state of the submission.
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
class_alias(PublishItemResponse::class, 'Google_Service_Chromewebstore_PublishItemResponse');
