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

namespace Google\Service\CloudAlloyDBAdmin;

class StorageDatabasecenterPartnerapiV1mainDatabaseResourceSignalData extends \Google\Model
{
  /**
   * Unspecified signal state.
   */
  public const SIGNAL_STATE_SIGNAL_STATE_UNSPECIFIED = 'SIGNAL_STATE_UNSPECIFIED';
  /**
   * Signal is active and requires attention.
   */
  public const SIGNAL_STATE_ACTIVE = 'ACTIVE';
  /**
   * Signal is inactive and does not require attention.
   */
  public const SIGNAL_STATE_INACTIVE = 'INACTIVE';
  /**
   * Signal is dismissed by the user and should not be shown to the user again.
   */
  public const SIGNAL_STATE_DISMISSED = 'DISMISSED';
  /**
   * Unspecified signal type.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_UNSPECIFIED = 'SIGNAL_TYPE_UNSPECIFIED';
  /**
   * Outdated Minor Version
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OUTDATED_MINOR_VERSION = 'SIGNAL_TYPE_OUTDATED_MINOR_VERSION';
  /**
   * Represents database auditing is disabled.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATABASE_AUDITING_DISABLED = 'SIGNAL_TYPE_DATABASE_AUDITING_DISABLED';
  /**
   * Represents if a database has a password configured for the root account or
   * not.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_ROOT_PASSWORD = 'SIGNAL_TYPE_NO_ROOT_PASSWORD';
  /**
   * Represents if a resource is exposed to public access.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS = 'SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS';
  /**
   * Represents if a resources requires all incoming connections to use SSL or
   * not.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS = 'SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS';
  /**
   * Represents if a resource version is in extended support.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXTENDED_SUPPORT = 'SIGNAL_TYPE_EXTENDED_SUPPORT';
  /**
   * Represents if a resource has no automated backup policy.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY = 'SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY';
  /**
   * Required. Full Resource name of the source resource.
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Required. Last time signal was refreshed
   *
   * @var string
   */
  public $lastRefreshTime;
  protected $resourceIdType = StorageDatabasecenterPartnerapiV1mainDatabaseResourceId::class;
  protected $resourceIdDataType = '';
  /**
   * Signal data for boolean signals.
   *
   * @var bool
   */
  public $signalBoolValue;
  /**
   * Required. Output only. Signal state of the signal
   *
   * @var string
   */
  public $signalState;
  /**
   * Required. Signal type of the signal
   *
   * @var string
   */
  public $signalType;

  /**
   * Required. Full Resource name of the source resource.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * Required. Last time signal was refreshed
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * Database resource id.
   *
   * @param StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId
   */
  public function setResourceId(StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainDatabaseResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * Signal data for boolean signals.
   *
   * @param bool $signalBoolValue
   */
  public function setSignalBoolValue($signalBoolValue)
  {
    $this->signalBoolValue = $signalBoolValue;
  }
  /**
   * @return bool
   */
  public function getSignalBoolValue()
  {
    return $this->signalBoolValue;
  }
  /**
   * Required. Output only. Signal state of the signal
   *
   * Accepted values: SIGNAL_STATE_UNSPECIFIED, ACTIVE, INACTIVE, DISMISSED
   *
   * @param self::SIGNAL_STATE_* $signalState
   */
  public function setSignalState($signalState)
  {
    $this->signalState = $signalState;
  }
  /**
   * @return self::SIGNAL_STATE_*
   */
  public function getSignalState()
  {
    return $this->signalState;
  }
  /**
   * Required. Signal type of the signal
   *
   * Accepted values: SIGNAL_TYPE_UNSPECIFIED,
   * SIGNAL_TYPE_OUTDATED_MINOR_VERSION, SIGNAL_TYPE_DATABASE_AUDITING_DISABLED,
   * SIGNAL_TYPE_NO_ROOT_PASSWORD, SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS,
   * SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS, SIGNAL_TYPE_EXTENDED_SUPPORT,
   * SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY
   *
   * @param self::SIGNAL_TYPE_* $signalType
   */
  public function setSignalType($signalType)
  {
    $this->signalType = $signalType;
  }
  /**
   * @return self::SIGNAL_TYPE_*
   */
  public function getSignalType()
  {
    return $this->signalType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainDatabaseResourceSignalData::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainDatabaseResourceSignalData');
