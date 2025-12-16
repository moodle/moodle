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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ResourceConfig extends \Google\Model
{
  /**
   * Default unspecified action type.
   */
  public const ACTION_TYPE_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * Action type for sync metadata.
   */
  public const ACTION_TYPE_SYNC_METADATA = 'SYNC_METADATA';
  /**
   * Action type for sync runtime data.
   */
  public const ACTION_TYPE_SYNC_RUNTIME_DATA = 'SYNC_RUNTIME_DATA';
  /**
   * Output only. The type of the action.
   *
   * @var string
   */
  public $actionType;
  /**
   * Output only. The pubsub topic to publish the data to. Format is
   * projects/{project}/topics/{topic}
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Output only. The type of the action.
   *
   * Accepted values: ACTION_TYPE_UNSPECIFIED, SYNC_METADATA, SYNC_RUNTIME_DATA
   *
   * @param self::ACTION_TYPE_* $actionType
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return self::ACTION_TYPE_*
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * Output only. The pubsub topic to publish the data to. Format is
   * projects/{project}/topics/{topic}
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
class_alias(GoogleCloudApihubV1ResourceConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1ResourceConfig');
