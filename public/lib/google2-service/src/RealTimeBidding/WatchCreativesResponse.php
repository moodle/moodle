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

class WatchCreativesResponse extends \Google\Model
{
  /**
   * The Pub/Sub subscription that can be used to pull creative status
   * notifications. This would be of the format
   * `projects/{project_id}/subscriptions/{subscription_id}`. Subscription is
   * created with pull delivery. All service accounts belonging to the bidder
   * will have read access to this subscription. Subscriptions that are inactive
   * for more than 90 days will be disabled. Use watchCreatives to re-enable the
   * subscription.
   *
   * @var string
   */
  public $subscription;
  /**
   * The Pub/Sub topic that will be used to publish creative serving status
   * notifications. This would be of the format
   * `projects/{project_id}/topics/{topic_id}`.
   *
   * @var string
   */
  public $topic;

  /**
   * The Pub/Sub subscription that can be used to pull creative status
   * notifications. This would be of the format
   * `projects/{project_id}/subscriptions/{subscription_id}`. Subscription is
   * created with pull delivery. All service accounts belonging to the bidder
   * will have read access to this subscription. Subscriptions that are inactive
   * for more than 90 days will be disabled. Use watchCreatives to re-enable the
   * subscription.
   *
   * @param string $subscription
   */
  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return string
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * The Pub/Sub topic that will be used to publish creative serving status
   * notifications. This would be of the format
   * `projects/{project_id}/topics/{topic_id}`.
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchCreativesResponse::class, 'Google_Service_RealTimeBidding_WatchCreativesResponse');
