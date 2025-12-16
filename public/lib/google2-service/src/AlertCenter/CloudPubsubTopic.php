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

namespace Google\Service\AlertCenter;

class CloudPubsubTopic extends \Google\Model
{
  /**
   * Payload format is not specified (will use JSON as default).
   */
  public const PAYLOAD_FORMAT_PAYLOAD_FORMAT_UNSPECIFIED = 'PAYLOAD_FORMAT_UNSPECIFIED';
  /**
   * Use JSON.
   */
  public const PAYLOAD_FORMAT_JSON = 'JSON';
  /**
   * Optional. The format of the payload that would be sent. If not specified
   * the format will be JSON.
   *
   * @var string
   */
  public $payloadFormat;
  /**
   * The `name` field of a Cloud Pubsub [Topic] (https://cloud.google.com/pubsub
   * /docs/reference/rest/v1/projects.topics#Topic).
   *
   * @var string
   */
  public $topicName;

  /**
   * Optional. The format of the payload that would be sent. If not specified
   * the format will be JSON.
   *
   * Accepted values: PAYLOAD_FORMAT_UNSPECIFIED, JSON
   *
   * @param self::PAYLOAD_FORMAT_* $payloadFormat
   */
  public function setPayloadFormat($payloadFormat)
  {
    $this->payloadFormat = $payloadFormat;
  }
  /**
   * @return self::PAYLOAD_FORMAT_*
   */
  public function getPayloadFormat()
  {
    return $this->payloadFormat;
  }
  /**
   * The `name` field of a Cloud Pubsub [Topic] (https://cloud.google.com/pubsub
   * /docs/reference/rest/v1/projects.topics#Topic).
   *
   * @param string $topicName
   */
  public function setTopicName($topicName)
  {
    $this->topicName = $topicName;
  }
  /**
   * @return string
   */
  public function getTopicName()
  {
    return $this->topicName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudPubsubTopic::class, 'Google_Service_AlertCenter_CloudPubsubTopic');
