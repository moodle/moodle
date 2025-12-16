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

class CreateSnapshotRequest extends \Google\Model
{
  /**
   * Optional. See [Creating and managing
   * labels](https://cloud.google.com/pubsub/docs/labels).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The subscription whose backlog the snapshot retains.
   * Specifically, the created snapshot is guaranteed to retain: (a) The
   * existing backlog on the subscription. More precisely, this is defined as
   * the messages in the subscription's backlog that are unacknowledged upon the
   * successful completion of the `CreateSnapshot` request; as well as: (b) Any
   * messages published to the subscription's topic following the successful
   * completion of the CreateSnapshot request. Format is
   * `projects/{project}/subscriptions/{sub}`.
   *
   * @var string
   */
  public $subscription;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. See [Creating and managing
   * labels](https://cloud.google.com/pubsub/docs/labels).
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
   * Required. The subscription whose backlog the snapshot retains.
   * Specifically, the created snapshot is guaranteed to retain: (a) The
   * existing backlog on the subscription. More precisely, this is defined as
   * the messages in the subscription's backlog that are unacknowledged upon the
   * successful completion of the `CreateSnapshot` request; as well as: (b) Any
   * messages published to the subscription's topic following the successful
   * completion of the CreateSnapshot request. Format is
   * `projects/{project}/subscriptions/{sub}`.
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
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateSnapshotRequest::class, 'Google_Service_Pubsub_CreateSnapshotRequest');
