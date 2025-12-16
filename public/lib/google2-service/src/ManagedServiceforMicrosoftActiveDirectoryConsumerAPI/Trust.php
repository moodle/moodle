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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class Trust extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The domain trust is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The domain trust is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The domain trust is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The domain trust is connected.
   */
  public const STATE_CONNECTED = 'CONNECTED';
  /**
   * The domain trust is disconnected.
   */
  public const STATE_DISCONNECTED = 'DISCONNECTED';
  /**
   * Not set.
   */
  public const TRUST_DIRECTION_TRUST_DIRECTION_UNSPECIFIED = 'TRUST_DIRECTION_UNSPECIFIED';
  /**
   * The inbound direction represents the trusting side.
   */
  public const TRUST_DIRECTION_INBOUND = 'INBOUND';
  /**
   * The outboud direction represents the trusted side.
   */
  public const TRUST_DIRECTION_OUTBOUND = 'OUTBOUND';
  /**
   * The bidirectional direction represents the trusted / trusting side.
   */
  public const TRUST_DIRECTION_BIDIRECTIONAL = 'BIDIRECTIONAL';
  /**
   * Not set.
   */
  public const TRUST_TYPE_TRUST_TYPE_UNSPECIFIED = 'TRUST_TYPE_UNSPECIFIED';
  /**
   * The forest trust.
   */
  public const TRUST_TYPE_FOREST = 'FOREST';
  /**
   * The external domain trust.
   */
  public const TRUST_TYPE_EXTERNAL = 'EXTERNAL';
  protected $collection_key = 'targetDnsIpAddresses';
  /**
   * Output only. The time the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The last heartbeat time when the trust was known to be
   * connected.
   *
   * @var string
   */
  public $lastTrustHeartbeatTime;
  /**
   * Optional. The trust authentication type, which decides whether the trusted
   * side has forest/domain wide access or selective access to an approved set
   * of resources.
   *
   * @var bool
   */
  public $selectiveAuthentication;
  /**
   * Output only. The current state of the trust.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the current state of the trust,
   * if available.
   *
   * @var string
   */
  public $stateDescription;
  /**
   * Required. The target DNS server IP addresses which can resolve the remote
   * domain involved in the trust.
   *
   * @var string[]
   */
  public $targetDnsIpAddresses;
  /**
   * Required. The fully qualified target domain name which will be in trust
   * with the current domain.
   *
   * @var string
   */
  public $targetDomainName;
  /**
   * Required. The trust direction, which decides if the current domain is
   * trusted, trusting, or both.
   *
   * @var string
   */
  public $trustDirection;
  /**
   * Required. The trust secret used for the handshake with the target domain.
   * This will not be stored.
   *
   * @var string
   */
  public $trustHandshakeSecret;
  /**
   * Required. The type of trust represented by the trust resource.
   *
   * @var string
   */
  public $trustType;
  /**
   * Output only. The last update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the instance was created.
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
   * Output only. The last heartbeat time when the trust was known to be
   * connected.
   *
   * @param string $lastTrustHeartbeatTime
   */
  public function setLastTrustHeartbeatTime($lastTrustHeartbeatTime)
  {
    $this->lastTrustHeartbeatTime = $lastTrustHeartbeatTime;
  }
  /**
   * @return string
   */
  public function getLastTrustHeartbeatTime()
  {
    return $this->lastTrustHeartbeatTime;
  }
  /**
   * Optional. The trust authentication type, which decides whether the trusted
   * side has forest/domain wide access or selective access to an approved set
   * of resources.
   *
   * @param bool $selectiveAuthentication
   */
  public function setSelectiveAuthentication($selectiveAuthentication)
  {
    $this->selectiveAuthentication = $selectiveAuthentication;
  }
  /**
   * @return bool
   */
  public function getSelectiveAuthentication()
  {
    return $this->selectiveAuthentication;
  }
  /**
   * Output only. The current state of the trust.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, UPDATING, DELETING,
   * CONNECTED, DISCONNECTED
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
   * Output only. Additional information about the current state of the trust,
   * if available.
   *
   * @param string $stateDescription
   */
  public function setStateDescription($stateDescription)
  {
    $this->stateDescription = $stateDescription;
  }
  /**
   * @return string
   */
  public function getStateDescription()
  {
    return $this->stateDescription;
  }
  /**
   * Required. The target DNS server IP addresses which can resolve the remote
   * domain involved in the trust.
   *
   * @param string[] $targetDnsIpAddresses
   */
  public function setTargetDnsIpAddresses($targetDnsIpAddresses)
  {
    $this->targetDnsIpAddresses = $targetDnsIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getTargetDnsIpAddresses()
  {
    return $this->targetDnsIpAddresses;
  }
  /**
   * Required. The fully qualified target domain name which will be in trust
   * with the current domain.
   *
   * @param string $targetDomainName
   */
  public function setTargetDomainName($targetDomainName)
  {
    $this->targetDomainName = $targetDomainName;
  }
  /**
   * @return string
   */
  public function getTargetDomainName()
  {
    return $this->targetDomainName;
  }
  /**
   * Required. The trust direction, which decides if the current domain is
   * trusted, trusting, or both.
   *
   * Accepted values: TRUST_DIRECTION_UNSPECIFIED, INBOUND, OUTBOUND,
   * BIDIRECTIONAL
   *
   * @param self::TRUST_DIRECTION_* $trustDirection
   */
  public function setTrustDirection($trustDirection)
  {
    $this->trustDirection = $trustDirection;
  }
  /**
   * @return self::TRUST_DIRECTION_*
   */
  public function getTrustDirection()
  {
    return $this->trustDirection;
  }
  /**
   * Required. The trust secret used for the handshake with the target domain.
   * This will not be stored.
   *
   * @param string $trustHandshakeSecret
   */
  public function setTrustHandshakeSecret($trustHandshakeSecret)
  {
    $this->trustHandshakeSecret = $trustHandshakeSecret;
  }
  /**
   * @return string
   */
  public function getTrustHandshakeSecret()
  {
    return $this->trustHandshakeSecret;
  }
  /**
   * Required. The type of trust represented by the trust resource.
   *
   * Accepted values: TRUST_TYPE_UNSPECIFIED, FOREST, EXTERNAL
   *
   * @param self::TRUST_TYPE_* $trustType
   */
  public function setTrustType($trustType)
  {
    $this->trustType = $trustType;
  }
  /**
   * @return self::TRUST_TYPE_*
   */
  public function getTrustType()
  {
    return $this->trustType;
  }
  /**
   * Output only. The last update time.
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
class_alias(Trust::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_Trust');
