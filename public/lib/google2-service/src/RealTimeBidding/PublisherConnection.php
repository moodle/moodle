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

namespace Google\Service\RealTimeBidding;

class PublisherConnection extends \Google\Model
{
  /**
   * An unspecified bidding status.
   */
  public const BIDDING_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates a request for connection from the publisher that the bidder needs
   * to review.
   */
  public const BIDDING_STATE_PENDING = 'PENDING';
  /**
   * Indicates that the publisher was rejected.
   */
  public const BIDDING_STATE_REJECTED = 'REJECTED';
  /**
   * Indicates that the publisher was approved.
   */
  public const BIDDING_STATE_APPROVED = 'APPROVED';
  /**
   * An unspecified publisher platform.
   */
  public const PUBLISHER_PLATFORM_PUBLISHER_PLATFORM_UNSPECIFIED = 'PUBLISHER_PLATFORM_UNSPECIFIED';
  /**
   * A Google Ad Manager publisher.
   */
  public const PUBLISHER_PLATFORM_GOOGLE_AD_MANAGER = 'GOOGLE_AD_MANAGER';
  /**
   * An AdMob publisher.
   */
  public const PUBLISHER_PLATFORM_ADMOB = 'ADMOB';
  /**
   * Whether the publisher has been approved by the bidder.
   *
   * @var string
   */
  public $biddingState;
  /**
   * Output only. The time at which the publisher initiated a connection with
   * the bidder (irrespective of if or when the bidder approves it). This is
   * subsequently updated if the publisher revokes and re-initiates the
   * connection.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Publisher display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Name of the publisher connection. This follows the pattern
   * `bidders/{bidder}/publisherConnections/{publisher}`, where `{bidder}`
   * represents the account ID of the bidder, and `{publisher}` is the
   * ads.txt/app-ads.txt publisher ID.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Whether the publisher is an Ad Manager or AdMob publisher.
   *
   * @var string
   */
  public $publisherPlatform;

  /**
   * Whether the publisher has been approved by the bidder.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, REJECTED, APPROVED
   *
   * @param self::BIDDING_STATE_* $biddingState
   */
  public function setBiddingState($biddingState)
  {
    $this->biddingState = $biddingState;
  }
  /**
   * @return self::BIDDING_STATE_*
   */
  public function getBiddingState()
  {
    return $this->biddingState;
  }
  /**
   * Output only. The time at which the publisher initiated a connection with
   * the bidder (irrespective of if or when the bidder approves it). This is
   * subsequently updated if the publisher revokes and re-initiates the
   * connection.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Publisher display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Name of the publisher connection. This follows the pattern
   * `bidders/{bidder}/publisherConnections/{publisher}`, where `{bidder}`
   * represents the account ID of the bidder, and `{publisher}` is the
   * ads.txt/app-ads.txt publisher ID.
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
   * Output only. Whether the publisher is an Ad Manager or AdMob publisher.
   *
   * Accepted values: PUBLISHER_PLATFORM_UNSPECIFIED, GOOGLE_AD_MANAGER, ADMOB
   *
   * @param self::PUBLISHER_PLATFORM_* $publisherPlatform
   */
  public function setPublisherPlatform($publisherPlatform)
  {
    $this->publisherPlatform = $publisherPlatform;
  }
  /**
   * @return self::PUBLISHER_PLATFORM_*
   */
  public function getPublisherPlatform()
  {
    return $this->publisherPlatform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublisherConnection::class, 'Google_Service_RealTimeBidding_PublisherConnection');
