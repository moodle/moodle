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

namespace Google\Service\AnalyticsHub;

class DeadLetterPolicy extends \Google\Model
{
  /**
   * Optional. The name of the topic to which dead letter messages should be
   * published. Format is `projects/{project}/topics/{topic}`.The Pub/Sub
   * service account associated with the enclosing subscription's parent project
   * (i.e., service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com) must
   * have permission to Publish() to this topic. The operation will fail if the
   * topic does not exist. Users should ensure that there is a subscription
   * attached to this topic since messages published to a topic with no
   * subscriptions are lost.
   *
   * @var string
   */
  public $deadLetterTopic;
  /**
   * Optional. The maximum number of delivery attempts for any message. The
   * value must be between 5 and 100. The number of delivery attempts is defined
   * as 1 + (the sum of number of NACKs and number of times the acknowledgement
   * deadline has been exceeded for the message). A NACK is any call to
   * ModifyAckDeadline with a 0 deadline. Note that client libraries may
   * automatically extend ack_deadlines. This field will be honored on a best
   * effort basis. If this parameter is 0, a default value of 5 is used.
   *
   * @var int
   */
  public $maxDeliveryAttempts;

  /**
   * Optional. The name of the topic to which dead letter messages should be
   * published. Format is `projects/{project}/topics/{topic}`.The Pub/Sub
   * service account associated with the enclosing subscription's parent project
   * (i.e., service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com) must
   * have permission to Publish() to this topic. The operation will fail if the
   * topic does not exist. Users should ensure that there is a subscription
   * attached to this topic since messages published to a topic with no
   * subscriptions are lost.
   *
   * @param string $deadLetterTopic
   */
  public function setDeadLetterTopic($deadLetterTopic)
  {
    $this->deadLetterTopic = $deadLetterTopic;
  }
  /**
   * @return string
   */
  public function getDeadLetterTopic()
  {
    return $this->deadLetterTopic;
  }
  /**
   * Optional. The maximum number of delivery attempts for any message. The
   * value must be between 5 and 100. The number of delivery attempts is defined
   * as 1 + (the sum of number of NACKs and number of times the acknowledgement
   * deadline has been exceeded for the message). A NACK is any call to
   * ModifyAckDeadline with a 0 deadline. Note that client libraries may
   * automatically extend ack_deadlines. This field will be honored on a best
   * effort basis. If this parameter is 0, a default value of 5 is used.
   *
   * @param int $maxDeliveryAttempts
   */
  public function setMaxDeliveryAttempts($maxDeliveryAttempts)
  {
    $this->maxDeliveryAttempts = $maxDeliveryAttempts;
  }
  /**
   * @return int
   */
  public function getMaxDeliveryAttempts()
  {
    return $this->maxDeliveryAttempts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeadLetterPolicy::class, 'Google_Service_AnalyticsHub_DeadLetterPolicy');
