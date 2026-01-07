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

namespace Google\Service\CloudWorkstations;

class WorkstationConfig extends \Google\Collection
{
  protected $collection_key = 'replicaZones';
  protected $allowedPortsType = PortRange::class;
  protected $allowedPortsDataType = 'array';
  /**
   * Optional. Client-specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  protected $conditionsType = Status::class;
  protected $conditionsDataType = 'array';
  protected $containerType = Container::class;
  protected $containerDataType = '';
  /**
   * Output only. Time when this workstation configuration was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Whether this workstation configuration is in degraded mode, in
   * which case it may require user action to restore full functionality. The
   * conditions field contains detailed information about the status of the
   * configuration.
   *
   * @var bool
   */
  public $degraded;
  /**
   * Output only. Time when this workstation configuration was soft-deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Disables support for plain TCP connections in the workstation. By
   * default the service supports TCP connections through a websocket relay.
   * Setting this option to true disables that relay, which prevents the usage
   * of services that require plain TCP connections, such as SSH. When enabled,
   * all communication must occur over HTTPS or WSS.
   *
   * @var bool
   */
  public $disableTcpConnections;
  /**
   * Optional. Human-readable name for this workstation configuration.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Whether to enable Linux `auditd` logging on the workstation. When
   * enabled, a service_account must also be specified that has
   * `roles/logging.logWriter` and `roles/monitoring.metricWriter` on the
   * project. Operating system audit logging is distinct from [Cloud Audit
   * Logs](https://cloud.google.com/workstations/docs/audit-logging) and
   * [Container output
   * logging](https://cloud.google.com/workstations/docs/container-output-
   * logging#overview). Operating system audit logs are available in the [Cloud
   * Logging](https://cloud.google.com/logging/docs) console by querying:
   * resource.type="gce_instance" log_name:"/logs/linux-auditd"
   *
   * @var bool
   */
  public $enableAuditAgent;
  protected $encryptionKeyType = CustomerEncryptionKey::class;
  protected $encryptionKeyDataType = '';
  protected $ephemeralDirectoriesType = EphemeralDirectory::class;
  protected $ephemeralDirectoriesDataType = 'array';
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Grant creator of a workstation `roles/workstations.policyAdmin`
   * role along with `roles/workstations.user` role on the workstation created
   * by them. This allows workstation users to share access to either their
   * entire workstation, or individual ports. Defaults to false.
   *
   * @var bool
   */
  public $grantWorkstationAdminRoleOnCreate;
  protected $hostType = Host::class;
  protected $hostDataType = '';
  /**
   * Optional. Number of seconds to wait before automatically stopping a
   * workstation after it last received user traffic. A value of `"0s"`
   * indicates that Cloud Workstations VMs created with this configuration
   * should never time out due to idleness. Provide
   * [duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#duration) terminated by `s` for
   * seconds—for example, `"7200s"` (2 hours). The default is `"1200s"` (20
   * minutes).
   *
   * @var string
   */
  public $idleTimeout;
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation configuration and that are
   * also propagated to the underlying Compute Engine resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Maximum number of workstations under this configuration a user
   * can have `workstations.workstation.use` permission on. Only enforced on
   * CreateWorkstation API calls on the user issuing the API request. Can be
   * overridden by: - granting a user
   * workstations.workstationConfigs.exemptMaxUsableWorkstationLimit permission,
   * or - having a user with that permission create a workstation and granting
   * another user `workstations.workstation.use` permission on that workstation.
   * If not specified, defaults to `0`, which indicates unlimited.
   *
   * @var int
   */
  public $maxUsableWorkstations;
  /**
   * Identifier. Full name of this workstation configuration.
   *
   * @var string
   */
  public $name;
  protected $persistentDirectoriesType = PersistentDirectory::class;
  protected $persistentDirectoriesDataType = 'array';
  protected $readinessChecksType = ReadinessCheck::class;
  protected $readinessChecksDataType = 'array';
  /**
   * Output only. Indicates whether this workstation configuration is currently
   * being updated to match its intended state.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Optional. Immutable. Specifies the zones used to replicate the VM and disk
   * resources within the region. If set, exactly two zones within the
   * workstation cluster's region must be specified—for example, `['us-
   * central1-a', 'us-central1-f']`. If this field is empty, two default zones
   * within the region are used. Immutable after the workstation configuration
   * is created.
   *
   * @var string[]
   */
  public $replicaZones;
  /**
   * Optional. Number of seconds that a workstation can run until it is
   * automatically shut down. We recommend that workstations be shut down daily
   * to reduce costs and so that security updates can be applied upon restart.
   * The idle_timeout and running_timeout fields are independent of each other.
   * Note that the running_timeout field shuts down VMs after the specified
   * time, regardless of whether or not the VMs are idle. Provide duration
   * terminated by `s` for seconds—for example, `"54000s"` (15 hours). Defaults
   * to `"43200s"` (12 hours). A value of `"0s"` indicates that workstations
   * using this configuration should never time out. If encryption_key is set,
   * it must be greater than `"0s"` and less than `"86400s"` (24 hours).
   * Warning: A value of `"0s"` indicates that Cloud Workstations VMs created
   * with this configuration have no maximum running time. This is strongly
   * discouraged because you incur costs and will not pick up security updates.
   *
   * @var string
   */
  public $runningTimeout;
  /**
   * Output only. A system-assigned unique identifier for this workstation
   * configuration.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time when this workstation configuration was most recently
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. A list of PortRanges specifying single ports or ranges of ports
   * that are externally accessible in the workstation. Allowed ports must be
   * one of 22, 80, or within range 1024-65535. If not specified defaults to
   * ports 22, 80, and ports 1024-65535.
   *
   * @param PortRange[] $allowedPorts
   */
  public function setAllowedPorts($allowedPorts)
  {
    $this->allowedPorts = $allowedPorts;
  }
  /**
   * @return PortRange[]
   */
  public function getAllowedPorts()
  {
    return $this->allowedPorts;
  }
  /**
   * Optional. Client-specified annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Status conditions describing the workstation configuration's
   * current state.
   *
   * @param Status[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return Status[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Optional. Container that runs upon startup for each workstation using this
   * workstation configuration.
   *
   * @param Container $container
   */
  public function setContainer(Container $container)
  {
    $this->container = $container;
  }
  /**
   * @return Container
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Output only. Time when this workstation configuration was created.
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
   * Output only. Whether this workstation configuration is in degraded mode, in
   * which case it may require user action to restore full functionality. The
   * conditions field contains detailed information about the status of the
   * configuration.
   *
   * @param bool $degraded
   */
  public function setDegraded($degraded)
  {
    $this->degraded = $degraded;
  }
  /**
   * @return bool
   */
  public function getDegraded()
  {
    return $this->degraded;
  }
  /**
   * Output only. Time when this workstation configuration was soft-deleted.
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
   * Optional. Disables support for plain TCP connections in the workstation. By
   * default the service supports TCP connections through a websocket relay.
   * Setting this option to true disables that relay, which prevents the usage
   * of services that require plain TCP connections, such as SSH. When enabled,
   * all communication must occur over HTTPS or WSS.
   *
   * @param bool $disableTcpConnections
   */
  public function setDisableTcpConnections($disableTcpConnections)
  {
    $this->disableTcpConnections = $disableTcpConnections;
  }
  /**
   * @return bool
   */
  public function getDisableTcpConnections()
  {
    return $this->disableTcpConnections;
  }
  /**
   * Optional. Human-readable name for this workstation configuration.
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
   * Optional. Whether to enable Linux `auditd` logging on the workstation. When
   * enabled, a service_account must also be specified that has
   * `roles/logging.logWriter` and `roles/monitoring.metricWriter` on the
   * project. Operating system audit logging is distinct from [Cloud Audit
   * Logs](https://cloud.google.com/workstations/docs/audit-logging) and
   * [Container output
   * logging](https://cloud.google.com/workstations/docs/container-output-
   * logging#overview). Operating system audit logs are available in the [Cloud
   * Logging](https://cloud.google.com/logging/docs) console by querying:
   * resource.type="gce_instance" log_name:"/logs/linux-auditd"
   *
   * @param bool $enableAuditAgent
   */
  public function setEnableAuditAgent($enableAuditAgent)
  {
    $this->enableAuditAgent = $enableAuditAgent;
  }
  /**
   * @return bool
   */
  public function getEnableAuditAgent()
  {
    return $this->enableAuditAgent;
  }
  /**
   * Immutable. Encrypts resources of this workstation configuration using a
   * customer-managed encryption key (CMEK). If specified, the boot disk of the
   * Compute Engine instance and the persistent disk are encrypted using this
   * encryption key. If this field is not set, the disks are encrypted using a
   * generated key. Customer-managed encryption keys do not protect disk
   * metadata. If the customer-managed encryption key is rotated, when the
   * workstation instance is stopped, the system attempts to recreate the
   * persistent disk with the new version of the key. Be sure to keep older
   * versions of the key until the persistent disk is recreated. Otherwise, data
   * on the persistent disk might be lost. If the encryption key is revoked, the
   * workstation session automatically stops within 7 hours. Immutable after the
   * workstation configuration is created.
   *
   * @param CustomerEncryptionKey $encryptionKey
   */
  public function setEncryptionKey(CustomerEncryptionKey $encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Optional. Ephemeral directories which won't persist across workstation
   * sessions.
   *
   * @param EphemeralDirectory[] $ephemeralDirectories
   */
  public function setEphemeralDirectories($ephemeralDirectories)
  {
    $this->ephemeralDirectories = $ephemeralDirectories;
  }
  /**
   * @return EphemeralDirectory[]
   */
  public function getEphemeralDirectories()
  {
    return $this->ephemeralDirectories;
  }
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
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
   * Optional. Grant creator of a workstation `roles/workstations.policyAdmin`
   * role along with `roles/workstations.user` role on the workstation created
   * by them. This allows workstation users to share access to either their
   * entire workstation, or individual ports. Defaults to false.
   *
   * @param bool $grantWorkstationAdminRoleOnCreate
   */
  public function setGrantWorkstationAdminRoleOnCreate($grantWorkstationAdminRoleOnCreate)
  {
    $this->grantWorkstationAdminRoleOnCreate = $grantWorkstationAdminRoleOnCreate;
  }
  /**
   * @return bool
   */
  public function getGrantWorkstationAdminRoleOnCreate()
  {
    return $this->grantWorkstationAdminRoleOnCreate;
  }
  /**
   * Optional. Runtime host for the workstation.
   *
   * @param Host $host
   */
  public function setHost(Host $host)
  {
    $this->host = $host;
  }
  /**
   * @return Host
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Optional. Number of seconds to wait before automatically stopping a
   * workstation after it last received user traffic. A value of `"0s"`
   * indicates that Cloud Workstations VMs created with this configuration
   * should never time out due to idleness. Provide
   * [duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#duration) terminated by `s` for
   * seconds—for example, `"7200s"` (2 hours). The default is `"1200s"` (20
   * minutes).
   *
   * @param string $idleTimeout
   */
  public function setIdleTimeout($idleTimeout)
  {
    $this->idleTimeout = $idleTimeout;
  }
  /**
   * @return string
   */
  public function getIdleTimeout()
  {
    return $this->idleTimeout;
  }
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation configuration and that are
   * also propagated to the underlying Compute Engine resources.
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
   * Optional. Maximum number of workstations under this configuration a user
   * can have `workstations.workstation.use` permission on. Only enforced on
   * CreateWorkstation API calls on the user issuing the API request. Can be
   * overridden by: - granting a user
   * workstations.workstationConfigs.exemptMaxUsableWorkstationLimit permission,
   * or - having a user with that permission create a workstation and granting
   * another user `workstations.workstation.use` permission on that workstation.
   * If not specified, defaults to `0`, which indicates unlimited.
   *
   * @param int $maxUsableWorkstations
   */
  public function setMaxUsableWorkstations($maxUsableWorkstations)
  {
    $this->maxUsableWorkstations = $maxUsableWorkstations;
  }
  /**
   * @return int
   */
  public function getMaxUsableWorkstations()
  {
    return $this->maxUsableWorkstations;
  }
  /**
   * Identifier. Full name of this workstation configuration.
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
   * Optional. Directories to persist across workstation sessions.
   *
   * @param PersistentDirectory[] $persistentDirectories
   */
  public function setPersistentDirectories($persistentDirectories)
  {
    $this->persistentDirectories = $persistentDirectories;
  }
  /**
   * @return PersistentDirectory[]
   */
  public function getPersistentDirectories()
  {
    return $this->persistentDirectories;
  }
  /**
   * Optional. Readiness checks to perform when starting a workstation using
   * this workstation configuration. Mark a workstation as running only after
   * all specified readiness checks return 200 status codes.
   *
   * @param ReadinessCheck[] $readinessChecks
   */
  public function setReadinessChecks($readinessChecks)
  {
    $this->readinessChecks = $readinessChecks;
  }
  /**
   * @return ReadinessCheck[]
   */
  public function getReadinessChecks()
  {
    return $this->readinessChecks;
  }
  /**
   * Output only. Indicates whether this workstation configuration is currently
   * being updated to match its intended state.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Optional. Immutable. Specifies the zones used to replicate the VM and disk
   * resources within the region. If set, exactly two zones within the
   * workstation cluster's region must be specified—for example, `['us-
   * central1-a', 'us-central1-f']`. If this field is empty, two default zones
   * within the region are used. Immutable after the workstation configuration
   * is created.
   *
   * @param string[] $replicaZones
   */
  public function setReplicaZones($replicaZones)
  {
    $this->replicaZones = $replicaZones;
  }
  /**
   * @return string[]
   */
  public function getReplicaZones()
  {
    return $this->replicaZones;
  }
  /**
   * Optional. Number of seconds that a workstation can run until it is
   * automatically shut down. We recommend that workstations be shut down daily
   * to reduce costs and so that security updates can be applied upon restart.
   * The idle_timeout and running_timeout fields are independent of each other.
   * Note that the running_timeout field shuts down VMs after the specified
   * time, regardless of whether or not the VMs are idle. Provide duration
   * terminated by `s` for seconds—for example, `"54000s"` (15 hours). Defaults
   * to `"43200s"` (12 hours). A value of `"0s"` indicates that workstations
   * using this configuration should never time out. If encryption_key is set,
   * it must be greater than `"0s"` and less than `"86400s"` (24 hours).
   * Warning: A value of `"0s"` indicates that Cloud Workstations VMs created
   * with this configuration have no maximum running time. This is strongly
   * discouraged because you incur costs and will not pick up security updates.
   *
   * @param string $runningTimeout
   */
  public function setRunningTimeout($runningTimeout)
  {
    $this->runningTimeout = $runningTimeout;
  }
  /**
   * @return string
   */
  public function getRunningTimeout()
  {
    return $this->runningTimeout;
  }
  /**
   * Output only. A system-assigned unique identifier for this workstation
   * configuration.
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
   * Output only. Time when this workstation configuration was most recently
   * updated.
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
class_alias(WorkstationConfig::class, 'Google_Service_CloudWorkstations_WorkstationConfig');
