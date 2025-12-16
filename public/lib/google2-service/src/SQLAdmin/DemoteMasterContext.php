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

namespace Google\Service\SQLAdmin;

class DemoteMasterContext extends \Google\Model
{
  /**
   * This is always `sql#demoteMasterContext`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the instance which will act as on-premises primary instance in
   * the replication setup.
   *
   * @var string
   */
  public $masterInstanceName;
  protected $replicaConfigurationType = DemoteMasterConfiguration::class;
  protected $replicaConfigurationDataType = '';
  /**
   * Flag to skip replication setup on the instance.
   *
   * @var bool
   */
  public $skipReplicationSetup;
  /**
   * Verify the GTID consistency for demote operation. Default value: `True`.
   * Setting this flag to `false` enables you to bypass the GTID consistency
   * check between on-premises primary instance and Cloud SQL instance during
   * the demotion operation but also exposes you to the risk of future
   * replication failures. Change the value only if you know the reason for the
   * GTID divergence and are confident that doing so will not cause any
   * replication issues.
   *
   * @var bool
   */
  public $verifyGtidConsistency;

  /**
   * This is always `sql#demoteMasterContext`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of the instance which will act as on-premises primary instance in
   * the replication setup.
   *
   * @param string $masterInstanceName
   */
  public function setMasterInstanceName($masterInstanceName)
  {
    $this->masterInstanceName = $masterInstanceName;
  }
  /**
   * @return string
   */
  public function getMasterInstanceName()
  {
    return $this->masterInstanceName;
  }
  /**
   * Configuration specific to read-replicas replicating from the on-premises
   * primary instance.
   *
   * @param DemoteMasterConfiguration $replicaConfiguration
   */
  public function setReplicaConfiguration(DemoteMasterConfiguration $replicaConfiguration)
  {
    $this->replicaConfiguration = $replicaConfiguration;
  }
  /**
   * @return DemoteMasterConfiguration
   */
  public function getReplicaConfiguration()
  {
    return $this->replicaConfiguration;
  }
  /**
   * Flag to skip replication setup on the instance.
   *
   * @param bool $skipReplicationSetup
   */
  public function setSkipReplicationSetup($skipReplicationSetup)
  {
    $this->skipReplicationSetup = $skipReplicationSetup;
  }
  /**
   * @return bool
   */
  public function getSkipReplicationSetup()
  {
    return $this->skipReplicationSetup;
  }
  /**
   * Verify the GTID consistency for demote operation. Default value: `True`.
   * Setting this flag to `false` enables you to bypass the GTID consistency
   * check between on-premises primary instance and Cloud SQL instance during
   * the demotion operation but also exposes you to the risk of future
   * replication failures. Change the value only if you know the reason for the
   * GTID divergence and are confident that doing so will not cause any
   * replication issues.
   *
   * @param bool $verifyGtidConsistency
   */
  public function setVerifyGtidConsistency($verifyGtidConsistency)
  {
    $this->verifyGtidConsistency = $verifyGtidConsistency;
  }
  /**
   * @return bool
   */
  public function getVerifyGtidConsistency()
  {
    return $this->verifyGtidConsistency;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DemoteMasterContext::class, 'Google_Service_SQLAdmin_DemoteMasterContext');
