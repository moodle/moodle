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

namespace Google\Service\SecretManager;

class SecretVersion extends \Google\Model
{
  /**
   * Not specified. This value is unused and invalid.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The SecretVersion may be accessed.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The SecretVersion may not be accessed, but the secret data is still
   * available and can be placed back into the ENABLED state.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The SecretVersion is destroyed and the secret data is no longer stored. A
   * version may not leave this state once entered.
   */
  public const STATE_DESTROYED = 'DESTROYED';
  /**
   * Output only. True if payload checksum specified in SecretPayload object has
   * been received by SecretManagerService on
   * SecretManagerService.AddSecretVersion.
   *
   * @var bool
   */
  public $clientSpecifiedPayloadChecksum;
  /**
   * Output only. The time at which the SecretVersion was created.
   *
   * @var string
   */
  public $createTime;
  protected $customerManagedEncryptionType = CustomerManagedEncryptionStatus::class;
  protected $customerManagedEncryptionDataType = '';
  /**
   * Output only. The time this SecretVersion was destroyed. Only present if
   * state is DESTROYED.
   *
   * @var string
   */
  public $destroyTime;
  /**
   * Output only. Etag of the currently stored SecretVersion.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The resource name of the SecretVersion in the format
   * `projects/secrets/versions`. SecretVersion IDs in a Secret start at 1 and
   * are incremented for each subsequent version of the secret.
   *
   * @var string
   */
  public $name;
  protected $replicationStatusType = ReplicationStatus::class;
  protected $replicationStatusDataType = '';
  /**
   * Optional. Output only. Scheduled destroy time for secret version. This is a
   * part of the Delayed secret version destroy feature. For a Secret with a
   * valid version destroy TTL, when a secert version is destroyed, version is
   * moved to disabled state and it is scheduled for destruction Version is
   * destroyed only after the scheduled_destroy_time.
   *
   * @var string
   */
  public $scheduledDestroyTime;
  /**
   * Output only. The current state of the SecretVersion.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. True if payload checksum specified in SecretPayload object has
   * been received by SecretManagerService on
   * SecretManagerService.AddSecretVersion.
   *
   * @param bool $clientSpecifiedPayloadChecksum
   */
  public function setClientSpecifiedPayloadChecksum($clientSpecifiedPayloadChecksum)
  {
    $this->clientSpecifiedPayloadChecksum = $clientSpecifiedPayloadChecksum;
  }
  /**
   * @return bool
   */
  public function getClientSpecifiedPayloadChecksum()
  {
    return $this->clientSpecifiedPayloadChecksum;
  }
  /**
   * Output only. The time at which the SecretVersion was created.
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
   * Output only. The customer-managed encryption status of the SecretVersion.
   * Only populated if customer-managed encryption is used and Secret is a
   * regionalized secret.
   *
   * @param CustomerManagedEncryptionStatus $customerManagedEncryption
   */
  public function setCustomerManagedEncryption(CustomerManagedEncryptionStatus $customerManagedEncryption)
  {
    $this->customerManagedEncryption = $customerManagedEncryption;
  }
  /**
   * @return CustomerManagedEncryptionStatus
   */
  public function getCustomerManagedEncryption()
  {
    return $this->customerManagedEncryption;
  }
  /**
   * Output only. The time this SecretVersion was destroyed. Only present if
   * state is DESTROYED.
   *
   * @param string $destroyTime
   */
  public function setDestroyTime($destroyTime)
  {
    $this->destroyTime = $destroyTime;
  }
  /**
   * @return string
   */
  public function getDestroyTime()
  {
    return $this->destroyTime;
  }
  /**
   * Output only. Etag of the currently stored SecretVersion.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The resource name of the SecretVersion in the format
   * `projects/secrets/versions`. SecretVersion IDs in a Secret start at 1 and
   * are incremented for each subsequent version of the secret.
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
   * The replication status of the SecretVersion.
   *
   * @param ReplicationStatus $replicationStatus
   */
  public function setReplicationStatus(ReplicationStatus $replicationStatus)
  {
    $this->replicationStatus = $replicationStatus;
  }
  /**
   * @return ReplicationStatus
   */
  public function getReplicationStatus()
  {
    return $this->replicationStatus;
  }
  /**
   * Optional. Output only. Scheduled destroy time for secret version. This is a
   * part of the Delayed secret version destroy feature. For a Secret with a
   * valid version destroy TTL, when a secert version is destroyed, version is
   * moved to disabled state and it is scheduled for destruction Version is
   * destroyed only after the scheduled_destroy_time.
   *
   * @param string $scheduledDestroyTime
   */
  public function setScheduledDestroyTime($scheduledDestroyTime)
  {
    $this->scheduledDestroyTime = $scheduledDestroyTime;
  }
  /**
   * @return string
   */
  public function getScheduledDestroyTime()
  {
    return $this->scheduledDestroyTime;
  }
  /**
   * Output only. The current state of the SecretVersion.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, DESTROYED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretVersion::class, 'Google_Service_SecretManager_SecretVersion');
