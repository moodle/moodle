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

namespace Google\Service\Eventarc;

class Pubsub extends \Google\Model
{
  /**
   * Output only. The name of the Pub/Sub subscription created and managed by
   * Eventarc as a transport for the event delivery. Format:
   * `projects/{PROJECT_ID}/subscriptions/{SUBSCRIPTION_NAME}`.
   *
   * @var string
   */
  public $subscription;
  /**
   * Optional. The name of the Pub/Sub topic created and managed by Eventarc as
   * a transport for the event delivery. Format:
   * `projects/{PROJECT_ID}/topics/{TOPIC_NAME}`. You can set an existing topic
   * for triggers of the type `google.cloud.pubsub.topic.v1.messagePublished`.
   * The topic you provide here is not deleted by Eventarc at trigger deletion.
   *
   * @var string
   */
  public $topic;

  /**
   * Output only. The name of the Pub/Sub subscription created and managed by
   * Eventarc as a transport for the event delivery. Format:
   * `projects/{PROJECT_ID}/subscriptions/{SUBSCRIPTION_NAME}`.
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
   * Optional. The name of the Pub/Sub topic created and managed by Eventarc as
   * a transport for the event delivery. Format:
   * `projects/{PROJECT_ID}/topics/{TOPIC_NAME}`. You can set an existing topic
   * for triggers of the type `google.cloud.pubsub.topic.v1.messagePublished`.
   * The topic you provide here is not deleted by Eventarc at trigger deletion.
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
class_alias(Pubsub::class, 'Google_Service_Eventarc_Pubsub');
