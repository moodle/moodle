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

namespace Google\Service\PubsubLite;

class ExportConfig extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const CURRENT_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Messages are being exported.
   */
  public const CURRENT_STATE_ACTIVE = 'ACTIVE';
  /**
   * Exporting messages is suspended.
   */
  public const CURRENT_STATE_PAUSED = 'PAUSED';
  /**
   * Messages cannot be exported due to permission denied errors. Output only.
   */
  public const CURRENT_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Messages cannot be exported due to missing resources. Output only.
   */
  public const CURRENT_STATE_NOT_FOUND = 'NOT_FOUND';
  /**
   * Default value. This value is unused.
   */
  public const DESIRED_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Messages are being exported.
   */
  public const DESIRED_STATE_ACTIVE = 'ACTIVE';
  /**
   * Exporting messages is suspended.
   */
  public const DESIRED_STATE_PAUSED = 'PAUSED';
  /**
   * Messages cannot be exported due to permission denied errors. Output only.
   */
  public const DESIRED_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Messages cannot be exported due to missing resources. Output only.
   */
  public const DESIRED_STATE_NOT_FOUND = 'NOT_FOUND';
  /**
   * Output only. The current state of the export, which may be different to the
   * desired state due to errors. This field is output only.
   *
   * @var string
   */
  public $currentState;
  /**
   * Optional. The name of an optional Pub/Sub Lite topic to publish messages
   * that can not be exported to the destination. For example, the message can
   * not be published to the Pub/Sub service because it does not satisfy the
   * constraints documented at https://cloud.google.com/pubsub/docs/publisher.
   * Structured like:
   * projects/{project_number}/locations/{location}/topics/{topic_id}. Must be
   * within the same project and location as the subscription. The topic may be
   * changed or removed.
   *
   * @var string
   */
  public $deadLetterTopic;
  /**
   * The desired state of this export. Setting this to values other than
   * `ACTIVE` and `PAUSED` will result in an error.
   *
   * @var string
   */
  public $desiredState;
  protected $pubsubConfigType = PubSubConfig::class;
  protected $pubsubConfigDataType = '';

  /**
   * Output only. The current state of the export, which may be different to the
   * desired state due to errors. This field is output only.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, PAUSED, PERMISSION_DENIED,
   * NOT_FOUND
   *
   * @param self::CURRENT_STATE_* $currentState
   */
  public function setCurrentState($currentState)
  {
    $this->currentState = $currentState;
  }
  /**
   * @return self::CURRENT_STATE_*
   */
  public function getCurrentState()
  {
    return $this->currentState;
  }
  /**
   * Optional. The name of an optional Pub/Sub Lite topic to publish messages
   * that can not be exported to the destination. For example, the message can
   * not be published to the Pub/Sub service because it does not satisfy the
   * constraints documented at https://cloud.google.com/pubsub/docs/publisher.
   * Structured like:
   * projects/{project_number}/locations/{location}/topics/{topic_id}. Must be
   * within the same project and location as the subscription. The topic may be
   * changed or removed.
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
   * The desired state of this export. Setting this to values other than
   * `ACTIVE` and `PAUSED` will result in an error.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, PAUSED, PERMISSION_DENIED,
   * NOT_FOUND
   *
   * @param self::DESIRED_STATE_* $desiredState
   */
  public function setDesiredState($desiredState)
  {
    $this->desiredState = $desiredState;
  }
  /**
   * @return self::DESIRED_STATE_*
   */
  public function getDesiredState()
  {
    return $this->desiredState;
  }
  /**
   * Messages are automatically written from the Pub/Sub Lite topic associated
   * with this subscription to a Pub/Sub topic.
   *
   * @param PubSubConfig $pubsubConfig
   */
  public function setPubsubConfig(PubSubConfig $pubsubConfig)
  {
    $this->pubsubConfig = $pubsubConfig;
  }
  /**
   * @return PubSubConfig
   */
  public function getPubsubConfig()
  {
    return $this->pubsubConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportConfig::class, 'Google_Service_PubsubLite_ExportConfig');
