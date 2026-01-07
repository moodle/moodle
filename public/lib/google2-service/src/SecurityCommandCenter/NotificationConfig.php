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

namespace Google\Service\SecurityCommandCenter;

class NotificationConfig extends \Google\Model
{
  /**
   * The description of the notification config (max of 1024 characters).
   *
   * @var string
   */
  public $description;
  /**
   * The relative resource name of this notification config. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example:
   * "organizations/{organization_id}/notificationConfigs/notify_public_bucket",
   * "folders/{folder_id}/notificationConfigs/notify_public_bucket", or
   * "projects/{project_id}/notificationConfigs/notify_public_bucket".
   *
   * @var string
   */
  public $name;
  /**
   * The Pub/Sub topic to send notifications to. Its format is
   * "projects/[project_id]/topics/[topic]".
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Output only. The service account that needs "pubsub.topics.publish"
   * permission to publish to the Pub/Sub topic.
   *
   * @var string
   */
  public $serviceAccount;
  protected $streamingConfigType = StreamingConfig::class;
  protected $streamingConfigDataType = '';

  /**
   * The description of the notification config (max of 1024 characters).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The relative resource name of this notification config. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example:
   * "organizations/{organization_id}/notificationConfigs/notify_public_bucket",
   * "folders/{folder_id}/notificationConfigs/notify_public_bucket", or
   * "projects/{project_id}/notificationConfigs/notify_public_bucket".
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
   * The Pub/Sub topic to send notifications to. Its format is
   * "projects/[project_id]/topics/[topic]".
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
   * Output only. The service account that needs "pubsub.topics.publish"
   * permission to publish to the Pub/Sub topic.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * The config for triggering streaming-based notifications.
   *
   * @param StreamingConfig $streamingConfig
   */
  public function setStreamingConfig(StreamingConfig $streamingConfig)
  {
    $this->streamingConfig = $streamingConfig;
  }
  /**
   * @return StreamingConfig
   */
  public function getStreamingConfig()
  {
    return $this->streamingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationConfig::class, 'Google_Service_SecurityCommandCenter_NotificationConfig');
