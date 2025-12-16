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

namespace Google\Service\CloudHealthcare;

class NotificationConfig extends \Google\Model
{
  /**
   * The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client.
   * PubsubMessage.Data contains the resource name. PubsubMessage.MessageId is
   * the ID of this message. It is guaranteed to be unique within the topic.
   * PubsubMessage.PublishTime is the time at which the message was published.
   * Notifications are only sent if the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. Cloud Healthcare API service account must have publisher
   * permissions on the given Pub/Sub topic. Not having adequate permissions
   * causes the calls that send notifications to fail. If a notification can't
   * be published to Pub/Sub, errors are logged to Cloud Logging (see [Viewing
   * error logs in Cloud Logging](https://cloud.google.com/healthcare/docs/how-
   * tos/logging)). If the number of errors exceeds a certain rate, some aren't
   * submitted. Note that not all operations trigger notifications, see
   * [Configuring Pub/Sub
   * notifications](https://cloud.google.com/healthcare/docs/how-tos/pubsub) for
   * specific details.
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Indicates whether or not to send Pub/Sub notifications on bulk import. Only
   * supported for DICOM imports.
   *
   * @var bool
   */
  public $sendForBulkImport;

  /**
   * The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client.
   * PubsubMessage.Data contains the resource name. PubsubMessage.MessageId is
   * the ID of this message. It is guaranteed to be unique within the topic.
   * PubsubMessage.PublishTime is the time at which the message was published.
   * Notifications are only sent if the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. Cloud Healthcare API service account must have publisher
   * permissions on the given Pub/Sub topic. Not having adequate permissions
   * causes the calls that send notifications to fail. If a notification can't
   * be published to Pub/Sub, errors are logged to Cloud Logging (see [Viewing
   * error logs in Cloud Logging](https://cloud.google.com/healthcare/docs/how-
   * tos/logging)). If the number of errors exceeds a certain rate, some aren't
   * submitted. Note that not all operations trigger notifications, see
   * [Configuring Pub/Sub
   * notifications](https://cloud.google.com/healthcare/docs/how-tos/pubsub) for
   * specific details.
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
  /**
   * Indicates whether or not to send Pub/Sub notifications on bulk import. Only
   * supported for DICOM imports.
   *
   * @param bool $sendForBulkImport
   */
  public function setSendForBulkImport($sendForBulkImport)
  {
    $this->sendForBulkImport = $sendForBulkImport;
  }
  /**
   * @return bool
   */
  public function getSendForBulkImport()
  {
    return $this->sendForBulkImport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationConfig::class, 'Google_Service_CloudHealthcare_NotificationConfig');
