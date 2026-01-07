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

namespace Google\Service\VMMigrationService;

class DatacenterConnector extends \Google\Model
{
  /**
   * The state is unknown. This is used for API compatibility only and is not
   * used by the system.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The state was not sampled by the health checks yet.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The source was sampled by health checks and is not available.
   */
  public const STATE_OFFLINE = 'OFFLINE';
  /**
   * The source is available but might not be usable yet due to unvalidated
   * credentials or another reason. The credentials referred to are the ones to
   * the Source. The error message will contain further details.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The source exists and its credentials were verified.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Output only. Appliance OVA version. This is the OVA which is manually
   * installed by the user and contains the infrastructure for the automatically
   * updatable components on the appliance.
   *
   * @var string
   */
  public $applianceInfrastructureVersion;
  /**
   * Output only. Appliance last installed update bundle version. This is the
   * version of the automatically updatable components on the appliance.
   *
   * @var string
   */
  public $applianceSoftwareVersion;
  protected $availableVersionsType = AvailableUpdates::class;
  protected $availableVersionsDataType = '';
  /**
   * Output only. The communication channel between the datacenter connector and
   * Google Cloud.
   *
   * @var string
   */
  public $bucket;
  /**
   * Output only. The time the connector was created (as an API call, not when
   * it was actually installed).
   *
   * @var string
   */
  public $createTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The connector's name.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. A unique key for this connector. This key is internal to the OVA
   * connector and is supplied with its creation during the registration process
   * and can not be modified.
   *
   * @var string
   */
  public $registrationId;
  /**
   * The service account to use in the connector when communicating with the
   * cloud.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. State of the DatacenterConnector, as determined by the health
   * checks.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time the state was last set.
   *
   * @var string
   */
  public $stateTime;
  /**
   * Output only. The last time the connector was updated with an API call.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeStatusType = UpgradeStatus::class;
  protected $upgradeStatusDataType = '';
  /**
   * The version running in the DatacenterConnector. This is supplied by the OVA
   * connector during the registration process and can not be modified.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Appliance OVA version. This is the OVA which is manually
   * installed by the user and contains the infrastructure for the automatically
   * updatable components on the appliance.
   *
   * @param string $applianceInfrastructureVersion
   */
  public function setApplianceInfrastructureVersion($applianceInfrastructureVersion)
  {
    $this->applianceInfrastructureVersion = $applianceInfrastructureVersion;
  }
  /**
   * @return string
   */
  public function getApplianceInfrastructureVersion()
  {
    return $this->applianceInfrastructureVersion;
  }
  /**
   * Output only. Appliance last installed update bundle version. This is the
   * version of the automatically updatable components on the appliance.
   *
   * @param string $applianceSoftwareVersion
   */
  public function setApplianceSoftwareVersion($applianceSoftwareVersion)
  {
    $this->applianceSoftwareVersion = $applianceSoftwareVersion;
  }
  /**
   * @return string
   */
  public function getApplianceSoftwareVersion()
  {
    return $this->applianceSoftwareVersion;
  }
  /**
   * Output only. The available versions for updating this appliance.
   *
   * @param AvailableUpdates $availableVersions
   */
  public function setAvailableVersions(AvailableUpdates $availableVersions)
  {
    $this->availableVersions = $availableVersions;
  }
  /**
   * @return AvailableUpdates
   */
  public function getAvailableVersions()
  {
    return $this->availableVersions;
  }
  /**
   * Output only. The communication channel between the datacenter connector and
   * Google Cloud.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Output only. The time the connector was created (as an API call, not when
   * it was actually installed).
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
   * Output only. Provides details on the state of the Datacenter Connector in
   * case of an error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The connector's name.
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
   * Immutable. A unique key for this connector. This key is internal to the OVA
   * connector and is supplied with its creation during the registration process
   * and can not be modified.
   *
   * @param string $registrationId
   */
  public function setRegistrationId($registrationId)
  {
    $this->registrationId = $registrationId;
  }
  /**
   * @return string
   */
  public function getRegistrationId()
  {
    return $this->registrationId;
  }
  /**
   * The service account to use in the connector when communicating with the
   * cloud.
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
   * Output only. State of the DatacenterConnector, as determined by the health
   * checks.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, OFFLINE, FAILED, ACTIVE
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
   * Output only. The time the state was last set.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Output only. The last time the connector was updated with an API call.
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
   * Output only. The status of the current / last upgradeAppliance operation.
   *
   * @param UpgradeStatus $upgradeStatus
   */
  public function setUpgradeStatus(UpgradeStatus $upgradeStatus)
  {
    $this->upgradeStatus = $upgradeStatus;
  }
  /**
   * @return UpgradeStatus
   */
  public function getUpgradeStatus()
  {
    return $this->upgradeStatus;
  }
  /**
   * The version running in the DatacenterConnector. This is supplied by the OVA
   * connector during the registration process and can not be modified.
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
class_alias(DatacenterConnector::class, 'Google_Service_VMMigrationService_DatacenterConnector');
