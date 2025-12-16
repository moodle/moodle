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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ImportCompletionDataRequest extends \Google\Model
{
  protected $inputConfigType = GoogleCloudRetailV2CompletionDataInputConfig::class;
  protected $inputConfigDataType = '';
  /**
   * Pub/Sub topic for receiving notification. If this field is set, when the
   * import is finished, a notification is sent to specified Pub/Sub topic. The
   * message data is JSON string of a Operation. Format of the Pub/Sub topic is
   * `projects/{project}/topics/{topic}`.
   *
   * @var string
   */
  public $notificationPubsubTopic;

  /**
   * Required. The desired input location of the data.
   *
   * @param GoogleCloudRetailV2CompletionDataInputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudRetailV2CompletionDataInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudRetailV2CompletionDataInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Pub/Sub topic for receiving notification. If this field is set, when the
   * import is finished, a notification is sent to specified Pub/Sub topic. The
   * message data is JSON string of a Operation. Format of the Pub/Sub topic is
   * `projects/{project}/topics/{topic}`.
   *
   * @param string $notificationPubsubTopic
   */
  public function setNotificationPubsubTopic($notificationPubsubTopic)
  {
    $this->notificationPubsubTopic = $notificationPubsubTopic;
  }
  /**
   * @return string
   */
  public function getNotificationPubsubTopic()
  {
    return $this->notificationPubsubTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ImportCompletionDataRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ImportCompletionDataRequest');
