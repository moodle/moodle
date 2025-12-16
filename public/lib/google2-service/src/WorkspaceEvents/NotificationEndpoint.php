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

namespace Google\Service\WorkspaceEvents;

class NotificationEndpoint extends \Google\Model
{
  /**
   * Immutable. The Pub/Sub topic that receives events for the subscription.
   * Format: `projects/{project}/topics/{topic}` You must create the topic in
   * the same Google Cloud project where you create this subscription. Note: The
   * Google Workspace Events API uses [ordering
   * keys](https://cloud.google.com/pubsub/docs/ordering) for the benefit of
   * sequential events. If the Cloud Pub/Sub topic has a [message storage
   * policy](https://cloud.google.com/pubsub/docs/resource-location-
   * restriction#exceptions) configured to exclude the nearest Google Cloud
   * region, publishing events with ordering keys will fail. When the topic
   * receives events, the events are encoded as Pub/Sub messages. For details,
   * see the [Google Cloud Pub/Sub Protocol Binding for
   * CloudEvents](https://github.com/googleapis/google-
   * cloudevents/blob/main/docs/spec/pubsub.md).
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Immutable. The Pub/Sub topic that receives events for the subscription.
   * Format: `projects/{project}/topics/{topic}` You must create the topic in
   * the same Google Cloud project where you create this subscription. Note: The
   * Google Workspace Events API uses [ordering
   * keys](https://cloud.google.com/pubsub/docs/ordering) for the benefit of
   * sequential events. If the Cloud Pub/Sub topic has a [message storage
   * policy](https://cloud.google.com/pubsub/docs/resource-location-
   * restriction#exceptions) configured to exclude the nearest Google Cloud
   * region, publishing events with ordering keys will fail. When the topic
   * receives events, the events are encoded as Pub/Sub messages. For details,
   * see the [Google Cloud Pub/Sub Protocol Binding for
   * CloudEvents](https://github.com/googleapis/google-
   * cloudevents/blob/main/docs/spec/pubsub.md).
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationEndpoint::class, 'Google_Service_WorkspaceEvents_NotificationEndpoint');
