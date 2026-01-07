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

namespace Google\Service\Pubsub;

class Snapshot extends \Google\Model
{
  /**
   * Optional. The snapshot is guaranteed to exist up until this time. A newly-
   * created snapshot expires no later than 7 days from the time of its
   * creation. Its exact lifetime is determined at creation by the existing
   * backlog in the source subscription. Specifically, the lifetime of the
   * snapshot is `7 days - (age of oldest unacked message in the subscription)`.
   * For example, consider a subscription whose oldest unacked message is 3 days
   * old. If a snapshot is created from this subscription, the snapshot -- which
   * will always capture this 3-day-old backlog as long as the snapshot exists
   * -- will expire in 4 days. The service will refuse to create a snapshot that
   * would expire in less than 1 hour after creation.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/pubsub/docs/labels).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The name of the snapshot.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name of the topic from which this snapshot is retaining
   * messages.
   *
   * @var string
   */
  public $topic;

  /**
   * Optional. The snapshot is guaranteed to exist up until this time. A newly-
   * created snapshot expires no later than 7 days from the time of its
   * creation. Its exact lifetime is determined at creation by the existing
   * backlog in the source subscription. Specifically, the lifetime of the
   * snapshot is `7 days - (age of oldest unacked message in the subscription)`.
   * For example, consider a subscription whose oldest unacked message is 3 days
   * old. If a snapshot is created from this subscription, the snapshot -- which
   * will always capture this 3-day-old backlog as long as the snapshot exists
   * -- will expire in 4 days. The service will refuse to create a snapshot that
   * would expire in less than 1 hour after creation.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/pubsub/docs/labels).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The name of the snapshot.
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
   * Optional. The name of the topic from which this snapshot is retaining
   * messages.
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
class_alias(Snapshot::class, 'Google_Service_Pubsub_Snapshot');
