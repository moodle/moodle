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

namespace Google\Service\WorkflowExecutions;

class TriggerPubsubExecutionRequest extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "gCPCloudEventsMode" => "GCPCloudEventsMode",
  ];
  /**
   * Required. LINT: LEGACY_NAMES The query parameter value for
   * __GCP_CloudEventsMode, set by the Eventarc service when configuring
   * triggers.
   *
   * @var string
   */
  public $gCPCloudEventsMode;
  /**
   * The number of attempts that have been made to deliver this message. This is
   * set by Pub/Sub for subscriptions that have the "dead letter" feature
   * enabled, and hence provided here for compatibility, but is ignored by
   * Workflows.
   *
   * @var int
   */
  public $deliveryAttempt;
  protected $messageType = PubsubMessage::class;
  protected $messageDataType = '';
  /**
   * Required. The subscription of the Pub/Sub push notification. Format:
   * projects/{project}/subscriptions/{sub}
   *
   * @var string
   */
  public $subscription;

  /**
   * Required. LINT: LEGACY_NAMES The query parameter value for
   * __GCP_CloudEventsMode, set by the Eventarc service when configuring
   * triggers.
   *
   * @param string $gCPCloudEventsMode
   */
  public function setGCPCloudEventsMode($gCPCloudEventsMode)
  {
    $this->gCPCloudEventsMode = $gCPCloudEventsMode;
  }
  /**
   * @return string
   */
  public function getGCPCloudEventsMode()
  {
    return $this->gCPCloudEventsMode;
  }
  /**
   * The number of attempts that have been made to deliver this message. This is
   * set by Pub/Sub for subscriptions that have the "dead letter" feature
   * enabled, and hence provided here for compatibility, but is ignored by
   * Workflows.
   *
   * @param int $deliveryAttempt
   */
  public function setDeliveryAttempt($deliveryAttempt)
  {
    $this->deliveryAttempt = $deliveryAttempt;
  }
  /**
   * @return int
   */
  public function getDeliveryAttempt()
  {
    return $this->deliveryAttempt;
  }
  /**
   * Required. The message of the Pub/Sub push notification.
   *
   * @param PubsubMessage $message
   */
  public function setMessage(PubsubMessage $message)
  {
    $this->message = $message;
  }
  /**
   * @return PubsubMessage
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Required. The subscription of the Pub/Sub push notification. Format:
   * projects/{project}/subscriptions/{sub}
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TriggerPubsubExecutionRequest::class, 'Google_Service_WorkflowExecutions_TriggerPubsubExecutionRequest');
