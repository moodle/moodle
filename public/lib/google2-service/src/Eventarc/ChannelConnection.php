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

namespace Google\Service\Eventarc;

class ChannelConnection extends \Google\Model
{
  /**
   * Input only. Activation token for the channel. The token will be used during
   * the creation of ChannelConnection to bind the channel with the provider
   * project. This field will not be stored in the provider resource.
   *
   * @var string
   */
  public $activationToken;
  /**
   * Required. The name of the connected subscriber Channel. This is a weak
   * reference to avoid cross project and cross accounts references. This must
   * be in `projects/{project}/location/{location}/channels/{channel_id}`
   * format.
   *
   * @var string
   */
  public $channel;
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The name of the connection.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Server assigned ID of the resource. The server guarantees
   * uniqueness and immutability until deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Input only. Activation token for the channel. The token will be used during
   * the creation of ChannelConnection to bind the channel with the provider
   * project. This field will not be stored in the provider resource.
   *
   * @param string $activationToken
   */
  public function setActivationToken($activationToken)
  {
    $this->activationToken = $activationToken;
  }
  /**
   * @return string
   */
  public function getActivationToken()
  {
    return $this->activationToken;
  }
  /**
   * Required. The name of the connected subscriber Channel. This is a weak
   * reference to avoid cross project and cross accounts references. This must
   * be in `projects/{project}/location/{location}/channels/{channel_id}`
   * format.
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Output only. The creation time.
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
   * Optional. Resource labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The name of the connection.
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
   * Output only. Server assigned ID of the resource. The server guarantees
   * uniqueness and immutability until deleted.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The last-modified time.
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
class_alias(ChannelConnection::class, 'Google_Service_Eventarc_ChannelConnection');
