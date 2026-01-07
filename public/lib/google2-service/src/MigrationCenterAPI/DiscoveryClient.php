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

namespace Google\Service\MigrationCenterAPI;

class DiscoveryClient extends \Google\Collection
{
  /**
   * Client state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Client is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Client is offline.
   */
  public const STATE_OFFLINE = 'OFFLINE';
  /**
   * Client is in a degraded state. See the `errors` field for details.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * Client has expired. See the expire_time field for the expire time.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  protected $collection_key = 'errors';
  /**
   * Output only. Time when the discovery client was first created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free text description. Maximum length is 1000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Free text display name. Maximum length is 63 characters.
   *
   * @var string
   */
  public $displayName;
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * Optional. Client expiration time in UTC. If specified, the backend will not
   * accept new frames after this time.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. Last heartbeat time. Healthy clients are expected to send
   * heartbeats regularly (normally every few minutes).
   *
   * @var string
   */
  public $heartbeatTime;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Full name of this discovery client.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Service account used by the discovery client for various
   * operation.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. This field is intended for internal use.
   *
   * @var string
   */
  public $signalsEndpoint;
  /**
   * Required. Immutable. Full name of the source object associated with this
   * discovery client.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. Current state of the discovery client.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Client time-to-live. If specified, the backend will
   * not accept new frames after this time. This field is input only. The
   * derived expiration time is provided as output through the `expire_time`
   * field.
   *
   * @var string
   */
  public $ttl;
  /**
   * Output only. Time when the discovery client was last updated. This value is
   * not updated by heartbeats, to view the last heartbeat time please refer to
   * the `heartbeat_time` field.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. Client version, as reported in recent heartbeat.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Time when the discovery client was first created.
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
   * Optional. Free text description. Maximum length is 1000 characters.
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
   * Optional. Free text display name. Maximum length is 63 characters.
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
   * Output only. Errors affecting client functionality.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Optional. Client expiration time in UTC. If specified, the backend will not
   * accept new frames after this time.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. Last heartbeat time. Healthy clients are expected to send
   * heartbeats regularly (normally every few minutes).
   *
   * @param string $heartbeatTime
   */
  public function setHeartbeatTime($heartbeatTime)
  {
    $this->heartbeatTime = $heartbeatTime;
  }
  /**
   * @return string
   */
  public function getHeartbeatTime()
  {
    return $this->heartbeatTime;
  }
  /**
   * Optional. Labels as key value pairs.
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
   * Output only. Identifier. Full name of this discovery client.
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
   * Required. Service account used by the discovery client for various
   * operation.
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
   * Output only. This field is intended for internal use.
   *
   * @param string $signalsEndpoint
   */
  public function setSignalsEndpoint($signalsEndpoint)
  {
    $this->signalsEndpoint = $signalsEndpoint;
  }
  /**
   * @return string
   */
  public function getSignalsEndpoint()
  {
    return $this->signalsEndpoint;
  }
  /**
   * Required. Immutable. Full name of the source object associated with this
   * discovery client.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. Current state of the discovery client.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, OFFLINE, DEGRADED, EXPIRED
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
   * Optional. Input only. Client time-to-live. If specified, the backend will
   * not accept new frames after this time. This field is input only. The
   * derived expiration time is provided as output through the `expire_time`
   * field.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Output only. Time when the discovery client was last updated. This value is
   * not updated by heartbeats, to view the last heartbeat time please refer to
   * the `heartbeat_time` field.
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
  /**
   * Output only. Client version, as reported in recent heartbeat.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveryClient::class, 'Google_Service_MigrationCenterAPI_DiscoveryClient');
