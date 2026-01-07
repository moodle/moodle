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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2BackupDisasterRecovery extends \Google\Collection
{
  protected $collection_key = 'policyOptions';
  /**
   * The name of the Backup and DR appliance that captures, moves, and manages
   * the lifecycle of backup data. For example, `backup-server-57137`.
   *
   * @var string
   */
  public $appliance;
  /**
   * The names of Backup and DR applications. An application is a VM, database,
   * or file system on a managed host monitored by a backup and recovery
   * appliance. For example, `centos7-01-vol00`, `centos7-01-vol01`,
   * `centos7-01-vol02`.
   *
   * @var string[]
   */
  public $applications;
  /**
   * The timestamp at which the Backup and DR backup was created.
   *
   * @var string
   */
  public $backupCreateTime;
  /**
   * The name of a Backup and DR template which comprises one or more backup
   * policies. See the [Backup and DR
   * documentation](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/backup-plan#temp) for more information. For example,
   * `snap-ov`.
   *
   * @var string
   */
  public $backupTemplate;
  /**
   * The backup type of the Backup and DR image. For example, `Snapshot`,
   * `Remote Snapshot`, `OnVault`.
   *
   * @var string
   */
  public $backupType;
  /**
   * The name of a Backup and DR host, which is managed by the backup and
   * recovery appliance and known to the management console. The host can be of
   * type Generic (for example, Compute Engine, SQL Server, Oracle DB, SMB file
   * system, etc.), vCenter, or an ESX server. See the [Backup and DR
   * documentation on hosts](https://cloud.google.com/backup-disaster-
   * recovery/docs/configuration/manage-hosts-and-their-applications) for more
   * information. For example, `centos7-01`.
   *
   * @var string
   */
  public $host;
  /**
   * The names of Backup and DR policies that are associated with a template and
   * that define when to run a backup, how frequently to run a backup, and how
   * long to retain the backup image. For example, `onvaults`.
   *
   * @var string[]
   */
  public $policies;
  /**
   * The names of Backup and DR advanced policy options of a policy applying to
   * an application. See the [Backup and DR documentation on policy
   * options](https://cloud.google.com/backup-disaster-recovery/docs/create-
   * plan/policy-settings). For example, `skipofflineappsincongrp, nounmap`.
   *
   * @var string[]
   */
  public $policyOptions;
  /**
   * The name of the Backup and DR resource profile that specifies the storage
   * media for backups of application and VM data. See the [Backup and DR
   * documentation on profiles](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/backup-plan#profile). For example, `GCP`.
   *
   * @var string
   */
  public $profile;
  /**
   * The name of the Backup and DR storage pool that the backup and recovery
   * appliance is storing data in. The storage pool could be of type Cloud,
   * Primary, Snapshot, or OnVault. See the [Backup and DR documentation on
   * storage pools](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/storage-pools). For example, `DiskPoolOne`.
   *
   * @var string
   */
  public $storagePool;

  /**
   * The name of the Backup and DR appliance that captures, moves, and manages
   * the lifecycle of backup data. For example, `backup-server-57137`.
   *
   * @param string $appliance
   */
  public function setAppliance($appliance)
  {
    $this->appliance = $appliance;
  }
  /**
   * @return string
   */
  public function getAppliance()
  {
    return $this->appliance;
  }
  /**
   * The names of Backup and DR applications. An application is a VM, database,
   * or file system on a managed host monitored by a backup and recovery
   * appliance. For example, `centos7-01-vol00`, `centos7-01-vol01`,
   * `centos7-01-vol02`.
   *
   * @param string[] $applications
   */
  public function setApplications($applications)
  {
    $this->applications = $applications;
  }
  /**
   * @return string[]
   */
  public function getApplications()
  {
    return $this->applications;
  }
  /**
   * The timestamp at which the Backup and DR backup was created.
   *
   * @param string $backupCreateTime
   */
  public function setBackupCreateTime($backupCreateTime)
  {
    $this->backupCreateTime = $backupCreateTime;
  }
  /**
   * @return string
   */
  public function getBackupCreateTime()
  {
    return $this->backupCreateTime;
  }
  /**
   * The name of a Backup and DR template which comprises one or more backup
   * policies. See the [Backup and DR
   * documentation](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/backup-plan#temp) for more information. For example,
   * `snap-ov`.
   *
   * @param string $backupTemplate
   */
  public function setBackupTemplate($backupTemplate)
  {
    $this->backupTemplate = $backupTemplate;
  }
  /**
   * @return string
   */
  public function getBackupTemplate()
  {
    return $this->backupTemplate;
  }
  /**
   * The backup type of the Backup and DR image. For example, `Snapshot`,
   * `Remote Snapshot`, `OnVault`.
   *
   * @param string $backupType
   */
  public function setBackupType($backupType)
  {
    $this->backupType = $backupType;
  }
  /**
   * @return string
   */
  public function getBackupType()
  {
    return $this->backupType;
  }
  /**
   * The name of a Backup and DR host, which is managed by the backup and
   * recovery appliance and known to the management console. The host can be of
   * type Generic (for example, Compute Engine, SQL Server, Oracle DB, SMB file
   * system, etc.), vCenter, or an ESX server. See the [Backup and DR
   * documentation on hosts](https://cloud.google.com/backup-disaster-
   * recovery/docs/configuration/manage-hosts-and-their-applications) for more
   * information. For example, `centos7-01`.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * The names of Backup and DR policies that are associated with a template and
   * that define when to run a backup, how frequently to run a backup, and how
   * long to retain the backup image. For example, `onvaults`.
   *
   * @param string[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return string[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
  /**
   * The names of Backup and DR advanced policy options of a policy applying to
   * an application. See the [Backup and DR documentation on policy
   * options](https://cloud.google.com/backup-disaster-recovery/docs/create-
   * plan/policy-settings). For example, `skipofflineappsincongrp, nounmap`.
   *
   * @param string[] $policyOptions
   */
  public function setPolicyOptions($policyOptions)
  {
    $this->policyOptions = $policyOptions;
  }
  /**
   * @return string[]
   */
  public function getPolicyOptions()
  {
    return $this->policyOptions;
  }
  /**
   * The name of the Backup and DR resource profile that specifies the storage
   * media for backups of application and VM data. See the [Backup and DR
   * documentation on profiles](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/backup-plan#profile). For example, `GCP`.
   *
   * @param string $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return string
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * The name of the Backup and DR storage pool that the backup and recovery
   * appliance is storing data in. The storage pool could be of type Cloud,
   * Primary, Snapshot, or OnVault. See the [Backup and DR documentation on
   * storage pools](https://cloud.google.com/backup-disaster-
   * recovery/docs/concepts/storage-pools). For example, `DiskPoolOne`.
   *
   * @param string $storagePool
   */
  public function setStoragePool($storagePool)
  {
    $this->storagePool = $storagePool;
  }
  /**
   * @return string
   */
  public function getStoragePool()
  {
    return $this->storagePool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2BackupDisasterRecovery::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2BackupDisasterRecovery');
