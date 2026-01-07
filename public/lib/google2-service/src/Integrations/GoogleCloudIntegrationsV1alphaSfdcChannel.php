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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaSfdcChannel extends \Google\Model
{
  /**
   * Required. The Channel topic defined by salesforce once an channel is opened
   *
   * @var string
   */
  public $channelTopic;
  /**
   * Output only. Time when the channel is created
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time when the channel was deleted. Empty if not deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. The description for this channel
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Client level unique name/alias to easily reference a channel.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Indicated if a channel has any active integrations referencing
   * it. Set to false when the channel is created, and set to true if there is
   * any integration published with the channel configured in it.
   *
   * @var bool
   */
  public $isActive;
  /**
   * Output only. Last sfdc messsage replay id for channel
   *
   * @var string
   */
  public $lastReplayId;
  /**
   * Resource name of the SFDC channel projects/{project}/locations/{location}/s
   * fdcInstances/{sfdc_instance}/sfdcChannels/{sfdc_channel}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time when the channel was last updated
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The Channel topic defined by salesforce once an channel is opened
   *
   * @param string $channelTopic
   */
  public function setChannelTopic($channelTopic)
  {
    $this->channelTopic = $channelTopic;
  }
  /**
   * @return string
   */
  public function getChannelTopic()
  {
    return $this->channelTopic;
  }
  /**
   * Output only. Time when the channel is created
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Time when the channel was deleted. Empty if not deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. The description for this channel
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
   * Optional. Client level unique name/alias to easily reference a channel.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Indicated if a channel has any active integrations referencing
   * it. Set to false when the channel is created, and set to true if there is
   * any integration published with the channel configured in it.
   *
   * @param bool $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
  /**
   * Output only. Last sfdc messsage replay id for channel
   *
   * @param string $lastReplayId
   */
  public function setLastReplayId($lastReplayId)
  {
    $this->lastReplayId = $lastReplayId;
  }
  /**
   * @return string
   */
  public function getLastReplayId()
  {
    return $this->lastReplayId;
  }
  /**
   * Resource name of the SFDC channel projects/{project}/locations/{location}/s
   * fdcInstances/{sfdc_instance}/sfdcChannels/{sfdc_channel}.
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
   * Output only. Time when the channel was last updated
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaSfdcChannel::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSfdcChannel');
