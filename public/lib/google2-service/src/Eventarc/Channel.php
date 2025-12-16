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

class Channel extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The PENDING state indicates that a Channel has been created successfully
   * and there is a new activation token available for the subscriber to use to
   * convey the Channel to the provider in order to create a Connection.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The ACTIVE state indicates that a Channel has been successfully connected
   * with the event provider. An ACTIVE Channel is ready to receive and route
   * events from the event provider.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The INACTIVE state indicates that the Channel cannot receive events
   * permanently. There are two possible cases this state can happen: 1. The
   * SaaS provider disconnected from this Channel. 2. The Channel activation
   * token has expired but the SaaS provider wasn't connected. To re-establish a
   * Connection with a provider, the subscriber should create a new Channel and
   * give it to the provider.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Output only. The activation token for the channel. The token must be used
   * by the provider to register the channel for publishing.
   *
   * @var string
   */
  public $activationToken;
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * Optional. Resource labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The resource name of the channel. Must be unique within the
   * location on the project and must be in
   * `projects/{project}/locations/{location}/channels/{channel_id}` format.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the event provider (e.g. Eventarc SaaS partner) associated with
   * the channel. This provider will be granted permissions to publish events to
   * the channel. Format:
   * `projects/{project}/locations/{location}/providers/{provider_id}`.
   *
   * @var string
   */
  public $provider;
  /**
   * Output only. The name of the Pub/Sub topic created and managed by Eventarc
   * system as a transport for the event delivery. Format:
   * `projects/{project}/topics/{topic_id}`.
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Output only. Whether or not this Channel satisfies the requirements of
   * physical zone separation
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The state of a Channel.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
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
   * Output only. The activation token for the channel. The token must be used
   * by the provider to register the channel for publishing.
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
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt their event data. It must match the pattern
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
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
   * Required. The resource name of the channel. Must be unique within the
   * location on the project and must be in
   * `projects/{project}/locations/{location}/channels/{channel_id}` format.
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
   * The name of the event provider (e.g. Eventarc SaaS partner) associated with
   * the channel. This provider will be granted permissions to publish events to
   * the channel. Format:
   * `projects/{project}/locations/{location}/providers/{provider_id}`.
   *
   * @param string $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Output only. The name of the Pub/Sub topic created and managed by Eventarc
   * system as a transport for the event delivery. Format:
   * `projects/{project}/topics/{topic_id}`.
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
   * Output only. Whether or not this Channel satisfies the requirements of
   * physical zone separation
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The state of a Channel.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ACTIVE, INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Server assigned unique identifier for the channel. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
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
class_alias(Channel::class, 'Google_Service_Eventarc_Channel');
