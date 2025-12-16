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

namespace Google\Service\Spanner;

class Instance extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const DEFAULT_BACKUP_SCHEDULE_TYPE_DEFAULT_BACKUP_SCHEDULE_TYPE_UNSPECIFIED = 'DEFAULT_BACKUP_SCHEDULE_TYPE_UNSPECIFIED';
  /**
   * A default backup schedule isn't created automatically when a new database
   * is created in the instance.
   */
  public const DEFAULT_BACKUP_SCHEDULE_TYPE_NONE = 'NONE';
  /**
   * A default backup schedule is created automatically when a new database is
   * created in the instance. The default backup schedule creates a full backup
   * every 24 hours. These full backups are retained for 7 days. You can edit or
   * delete the default backup schedule once it's created.
   */
  public const DEFAULT_BACKUP_SCHEDULE_TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Edition not specified.
   */
  public const EDITION_EDITION_UNSPECIFIED = 'EDITION_UNSPECIFIED';
  /**
   * Standard edition.
   */
  public const EDITION_STANDARD = 'STANDARD';
  /**
   * Enterprise edition.
   */
  public const EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * Enterprise Plus edition.
   */
  public const EDITION_ENTERPRISE_PLUS = 'ENTERPRISE_PLUS';
  /**
   * Not specified.
   */
  public const INSTANCE_TYPE_INSTANCE_TYPE_UNSPECIFIED = 'INSTANCE_TYPE_UNSPECIFIED';
  /**
   * Provisioned instances have dedicated resources, standard usage limits and
   * support.
   */
  public const INSTANCE_TYPE_PROVISIONED = 'PROVISIONED';
  /**
   * Free instances provide no guarantee for dedicated resources, [node_count,
   * processing_units] should be 0. They come with stricter usage limits and
   * limited support.
   */
  public const INSTANCE_TYPE_FREE_INSTANCE = 'FREE_INSTANCE';
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is still being created. Resources may not be available yet,
   * and operations such as database creation may not work.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance is fully created and ready to do work such as creating
   * databases.
   */
  public const STATE_READY = 'READY';
  protected $collection_key = 'replicaComputeCapacity';
  protected $autoscalingConfigType = AutoscalingConfig::class;
  protected $autoscalingConfigDataType = '';
  /**
   * Required. The name of the instance's configuration. Values are of the form
   * `projects//instanceConfigs/`. See also InstanceConfig and
   * ListInstanceConfigs.
   *
   * @var string
   */
  public $config;
  /**
   * Output only. The time at which the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Controls the default backup schedule behavior for new databases
   * within the instance. By default, a backup schedule is created automatically
   * when a new database is created in a new instance. Note that the `AUTOMATIC`
   * value isn't permitted for free instances, as backups and backup schedules
   * aren't supported for free instances. In the `GetInstance` or
   * `ListInstances` response, if the value of `default_backup_schedule_type`
   * isn't set, or set to `NONE`, Spanner doesn't create a default backup
   * schedule for new databases in the instance.
   *
   * @var string
   */
  public $defaultBackupScheduleType;
  /**
   * Required. The descriptive name for this instance as it appears in UIs. Must
   * be unique per project and between 4 and 30 characters in length.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The `Edition` of the current instance.
   *
   * @var string
   */
  public $edition;
  /**
   * Deprecated. This field is not populated.
   *
   * @var string[]
   */
  public $endpointUris;
  protected $freeInstanceMetadataType = FreeInstanceMetadata::class;
  protected $freeInstanceMetadataDataType = '';
  /**
   * The `InstanceType` of the current instance.
   *
   * @var string
   */
  public $instanceType;
  /**
   * Cloud Labels are a flexible and lightweight mechanism for organizing cloud
   * resources into groups that reflect a customer's organizational needs and
   * deployment strategies. Cloud Labels can be used to filter collections of
   * resources. They can be used to control how resource metrics are aggregated.
   * And they can be used as arguments to policy management rules (e.g. route,
   * firewall, load balancing, etc.). * Label keys must be between 1 and 63
   * characters long and must conform to the following regular expression:
   * `a-z{0,62}`. * Label values must be between 0 and 63 characters long and
   * must conform to the regular expression `[a-z0-9_-]{0,63}`. * No more than
   * 64 labels can be associated with a given resource. See
   * https://goo.gl/xmQnxf for more information on and examples of labels. If
   * you plan to use labels in your own code, please note that additional
   * characters may be allowed in the future. And so you are advised to use an
   * internal label representation, such as JSON, which doesn't rely upon
   * specific characters being disallowed. For example, representing labels as
   * the string: name + "_" + value would prove problematic if we were to allow
   * "_" in a future release.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. A unique identifier for the instance, which cannot be changed
   * after the instance is created. Values are of the form
   * `projects//instances/a-z*[a-z0-9]`. The final segment of the name must be
   * between 2 and 64 characters in length.
   *
   * @var string
   */
  public $name;
  /**
   * The number of nodes allocated to this instance. At most, one of either
   * `node_count` or `processing_units` should be present in the message. Users
   * can set the `node_count` field to specify the target number of nodes
   * allocated to the instance. If autoscaling is enabled, `node_count` is
   * treated as an `OUTPUT_ONLY` field and reflects the current number of nodes
   * allocated to the instance. This might be zero in API responses for
   * instances that are not yet in the `READY` state. If the instance has
   * varying node count across replicas (achieved by setting
   * `asymmetric_autoscaling_options` in the autoscaling configuration), the
   * `node_count` set here is the maximum node count across all replicas. For
   * more information, see [Compute capacity, nodes, and processing
   * units](https://cloud.google.com/spanner/docs/compute-capacity).
   *
   * @var int
   */
  public $nodeCount;
  /**
   * The number of processing units allocated to this instance. At most, one of
   * either `processing_units` or `node_count` should be present in the message.
   * Users can set the `processing_units` field to specify the target number of
   * processing units allocated to the instance. If autoscaling is enabled,
   * `processing_units` is treated as an `OUTPUT_ONLY` field and reflects the
   * current number of processing units allocated to the instance. This might be
   * zero in API responses for instances that are not yet in the `READY` state.
   * If the instance has varying processing units per replica (achieved by
   * setting `asymmetric_autoscaling_options` in the autoscaling configuration),
   * the `processing_units` set here is the maximum processing units across all
   * replicas. For more information, see [Compute capacity, nodes and processing
   * units](https://cloud.google.com/spanner/docs/compute-capacity).
   *
   * @var int
   */
  public $processingUnits;
  protected $replicaComputeCapacityType = ReplicaComputeCapacity::class;
  protected $replicaComputeCapacityDataType = 'array';
  /**
   * Output only. The current instance state. For CreateInstance, the state must
   * be either omitted or set to `CREATING`. For UpdateInstance, the state must
   * be either omitted or set to `READY`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the instance was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The autoscaling configuration. Autoscaling is enabled if this
   * field is set. When autoscaling is enabled, node_count and processing_units
   * are treated as OUTPUT_ONLY fields and reflect the current compute capacity
   * allocated to the instance.
   *
   * @param AutoscalingConfig $autoscalingConfig
   */
  public function setAutoscalingConfig(AutoscalingConfig $autoscalingConfig)
  {
    $this->autoscalingConfig = $autoscalingConfig;
  }
  /**
   * @return AutoscalingConfig
   */
  public function getAutoscalingConfig()
  {
    return $this->autoscalingConfig;
  }
  /**
   * Required. The name of the instance's configuration. Values are of the form
   * `projects//instanceConfigs/`. See also InstanceConfig and
   * ListInstanceConfigs.
   *
   * @param string $config
   */
  public function setConfig($config)
  {
    $this->config = $config;
  }
  /**
   * @return string
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which the instance was created.
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
   * Optional. Controls the default backup schedule behavior for new databases
   * within the instance. By default, a backup schedule is created automatically
   * when a new database is created in a new instance. Note that the `AUTOMATIC`
   * value isn't permitted for free instances, as backups and backup schedules
   * aren't supported for free instances. In the `GetInstance` or
   * `ListInstances` response, if the value of `default_backup_schedule_type`
   * isn't set, or set to `NONE`, Spanner doesn't create a default backup
   * schedule for new databases in the instance.
   *
   * Accepted values: DEFAULT_BACKUP_SCHEDULE_TYPE_UNSPECIFIED, NONE, AUTOMATIC
   *
   * @param self::DEFAULT_BACKUP_SCHEDULE_TYPE_* $defaultBackupScheduleType
   */
  public function setDefaultBackupScheduleType($defaultBackupScheduleType)
  {
    $this->defaultBackupScheduleType = $defaultBackupScheduleType;
  }
  /**
   * @return self::DEFAULT_BACKUP_SCHEDULE_TYPE_*
   */
  public function getDefaultBackupScheduleType()
  {
    return $this->defaultBackupScheduleType;
  }
  /**
   * Required. The descriptive name for this instance as it appears in UIs. Must
   * be unique per project and between 4 and 30 characters in length.
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
   * Optional. The `Edition` of the current instance.
   *
   * Accepted values: EDITION_UNSPECIFIED, STANDARD, ENTERPRISE, ENTERPRISE_PLUS
   *
   * @param self::EDITION_* $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return self::EDITION_*
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Deprecated. This field is not populated.
   *
   * @param string[] $endpointUris
   */
  public function setEndpointUris($endpointUris)
  {
    $this->endpointUris = $endpointUris;
  }
  /**
   * @return string[]
   */
  public function getEndpointUris()
  {
    return $this->endpointUris;
  }
  /**
   * Free instance metadata. Only populated for free instances.
   *
   * @param FreeInstanceMetadata $freeInstanceMetadata
   */
  public function setFreeInstanceMetadata(FreeInstanceMetadata $freeInstanceMetadata)
  {
    $this->freeInstanceMetadata = $freeInstanceMetadata;
  }
  /**
   * @return FreeInstanceMetadata
   */
  public function getFreeInstanceMetadata()
  {
    return $this->freeInstanceMetadata;
  }
  /**
   * The `InstanceType` of the current instance.
   *
   * Accepted values: INSTANCE_TYPE_UNSPECIFIED, PROVISIONED, FREE_INSTANCE
   *
   * @param self::INSTANCE_TYPE_* $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return self::INSTANCE_TYPE_*
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Cloud Labels are a flexible and lightweight mechanism for organizing cloud
   * resources into groups that reflect a customer's organizational needs and
   * deployment strategies. Cloud Labels can be used to filter collections of
   * resources. They can be used to control how resource metrics are aggregated.
   * And they can be used as arguments to policy management rules (e.g. route,
   * firewall, load balancing, etc.). * Label keys must be between 1 and 63
   * characters long and must conform to the following regular expression:
   * `a-z{0,62}`. * Label values must be between 0 and 63 characters long and
   * must conform to the regular expression `[a-z0-9_-]{0,63}`. * No more than
   * 64 labels can be associated with a given resource. See
   * https://goo.gl/xmQnxf for more information on and examples of labels. If
   * you plan to use labels in your own code, please note that additional
   * characters may be allowed in the future. And so you are advised to use an
   * internal label representation, such as JSON, which doesn't rely upon
   * specific characters being disallowed. For example, representing labels as
   * the string: name + "_" + value would prove problematic if we were to allow
   * "_" in a future release.
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
   * Required. A unique identifier for the instance, which cannot be changed
   * after the instance is created. Values are of the form
   * `projects//instances/a-z*[a-z0-9]`. The final segment of the name must be
   * between 2 and 64 characters in length.
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
   * The number of nodes allocated to this instance. At most, one of either
   * `node_count` or `processing_units` should be present in the message. Users
   * can set the `node_count` field to specify the target number of nodes
   * allocated to the instance. If autoscaling is enabled, `node_count` is
   * treated as an `OUTPUT_ONLY` field and reflects the current number of nodes
   * allocated to the instance. This might be zero in API responses for
   * instances that are not yet in the `READY` state. If the instance has
   * varying node count across replicas (achieved by setting
   * `asymmetric_autoscaling_options` in the autoscaling configuration), the
   * `node_count` set here is the maximum node count across all replicas. For
   * more information, see [Compute capacity, nodes, and processing
   * units](https://cloud.google.com/spanner/docs/compute-capacity).
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * The number of processing units allocated to this instance. At most, one of
   * either `processing_units` or `node_count` should be present in the message.
   * Users can set the `processing_units` field to specify the target number of
   * processing units allocated to the instance. If autoscaling is enabled,
   * `processing_units` is treated as an `OUTPUT_ONLY` field and reflects the
   * current number of processing units allocated to the instance. This might be
   * zero in API responses for instances that are not yet in the `READY` state.
   * If the instance has varying processing units per replica (achieved by
   * setting `asymmetric_autoscaling_options` in the autoscaling configuration),
   * the `processing_units` set here is the maximum processing units across all
   * replicas. For more information, see [Compute capacity, nodes and processing
   * units](https://cloud.google.com/spanner/docs/compute-capacity).
   *
   * @param int $processingUnits
   */
  public function setProcessingUnits($processingUnits)
  {
    $this->processingUnits = $processingUnits;
  }
  /**
   * @return int
   */
  public function getProcessingUnits()
  {
    return $this->processingUnits;
  }
  /**
   * Output only. Lists the compute capacity per ReplicaSelection. A replica
   * selection identifies a set of replicas with common properties. Replicas
   * identified by a ReplicaSelection are scaled with the same compute capacity.
   *
   * @param ReplicaComputeCapacity[] $replicaComputeCapacity
   */
  public function setReplicaComputeCapacity($replicaComputeCapacity)
  {
    $this->replicaComputeCapacity = $replicaComputeCapacity;
  }
  /**
   * @return ReplicaComputeCapacity[]
   */
  public function getReplicaComputeCapacity()
  {
    return $this->replicaComputeCapacity;
  }
  /**
   * Output only. The current instance state. For CreateInstance, the state must
   * be either omitted or set to `CREATING`. For UpdateInstance, the state must
   * be either omitted or set to `READY`.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY
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
   * Output only. The time at which the instance was most recently updated.
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
class_alias(Instance::class, 'Google_Service_Spanner_Instance');
