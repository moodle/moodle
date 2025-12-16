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

class InstancePartition extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance partition is still being created. Resources may not be
   * available yet, and operations such as creating placements using this
   * instance partition may not work.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance partition is fully created and ready to do work such as
   * creating placements and using in databases.
   */
  public const STATE_READY = 'READY';
  protected $collection_key = 'referencingDatabases';
  protected $autoscalingConfigType = AutoscalingConfig::class;
  protected $autoscalingConfigDataType = '';
  /**
   * Required. The name of the instance partition's configuration. Values are of
   * the form `projects//instanceConfigs/`. See also InstanceConfig and
   * ListInstanceConfigs.
   *
   * @var string
   */
  public $config;
  /**
   * Output only. The time at which the instance partition was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The descriptive name for this instance partition as it appears in
   * UIs. Must be unique per project and between 4 and 30 characters in length.
   *
   * @var string
   */
  public $displayName;
  /**
   * Used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a instance partition from overwriting each other.
   * It is strongly suggested that systems make use of the etag in the read-
   * modify-write cycle to perform instance partition updates in order to avoid
   * race conditions: An etag is returned in the response which contains
   * instance partitions, and systems are expected to put that etag in the
   * request to update instance partitions to ensure that their change will be
   * applied to the same version of the instance partition. If no etag is
   * provided in the call to update instance partition, then the existing
   * instance partition is overwritten blindly.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. A unique identifier for the instance partition. Values are of the
   * form `projects//instances//instancePartitions/a-z*[a-z0-9]`. The final
   * segment of the name must be between 2 and 64 characters in length. An
   * instance partition's name cannot be changed after the instance partition is
   * created.
   *
   * @var string
   */
  public $name;
  /**
   * The number of nodes allocated to this instance partition. Users can set the
   * `node_count` field to specify the target number of nodes allocated to the
   * instance partition. This may be zero in API responses for instance
   * partitions that are not yet in state `READY`.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * The number of processing units allocated to this instance partition. Users
   * can set the `processing_units` field to specify the target number of
   * processing units allocated to the instance partition. This might be zero in
   * API responses for instance partitions that are not yet in the `READY`
   * state.
   *
   * @var int
   */
  public $processingUnits;
  /**
   * Output only. Deprecated: This field is not populated. Output only. The
   * names of the backups that reference this instance partition. Referencing
   * backups should share the parent instance. The existence of any referencing
   * backup prevents the instance partition from being deleted.
   *
   * @deprecated
   * @var string[]
   */
  public $referencingBackups;
  /**
   * Output only. The names of the databases that reference this instance
   * partition. Referencing databases should share the parent instance. The
   * existence of any referencing database prevents the instance partition from
   * being deleted.
   *
   * @var string[]
   */
  public $referencingDatabases;
  /**
   * Output only. The current instance partition state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the instance partition was most recently
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The autoscaling configuration. Autoscaling is enabled if this
   * field is set. When autoscaling is enabled, fields in compute_capacity are
   * treated as OUTPUT_ONLY fields and reflect the current compute capacity
   * allocated to the instance partition.
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
   * Required. The name of the instance partition's configuration. Values are of
   * the form `projects//instanceConfigs/`. See also InstanceConfig and
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
   * Output only. The time at which the instance partition was created.
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
   * Required. The descriptive name for this instance partition as it appears in
   * UIs. Must be unique per project and between 4 and 30 characters in length.
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
   * Used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a instance partition from overwriting each other.
   * It is strongly suggested that systems make use of the etag in the read-
   * modify-write cycle to perform instance partition updates in order to avoid
   * race conditions: An etag is returned in the response which contains
   * instance partitions, and systems are expected to put that etag in the
   * request to update instance partitions to ensure that their change will be
   * applied to the same version of the instance partition. If no etag is
   * provided in the call to update instance partition, then the existing
   * instance partition is overwritten blindly.
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
   * Required. A unique identifier for the instance partition. Values are of the
   * form `projects//instances//instancePartitions/a-z*[a-z0-9]`. The final
   * segment of the name must be between 2 and 64 characters in length. An
   * instance partition's name cannot be changed after the instance partition is
   * created.
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
   * The number of nodes allocated to this instance partition. Users can set the
   * `node_count` field to specify the target number of nodes allocated to the
   * instance partition. This may be zero in API responses for instance
   * partitions that are not yet in state `READY`.
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
   * The number of processing units allocated to this instance partition. Users
   * can set the `processing_units` field to specify the target number of
   * processing units allocated to the instance partition. This might be zero in
   * API responses for instance partitions that are not yet in the `READY`
   * state.
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
   * Output only. Deprecated: This field is not populated. Output only. The
   * names of the backups that reference this instance partition. Referencing
   * backups should share the parent instance. The existence of any referencing
   * backup prevents the instance partition from being deleted.
   *
   * @deprecated
   * @param string[] $referencingBackups
   */
  public function setReferencingBackups($referencingBackups)
  {
    $this->referencingBackups = $referencingBackups;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getReferencingBackups()
  {
    return $this->referencingBackups;
  }
  /**
   * Output only. The names of the databases that reference this instance
   * partition. Referencing databases should share the parent instance. The
   * existence of any referencing database prevents the instance partition from
   * being deleted.
   *
   * @param string[] $referencingDatabases
   */
  public function setReferencingDatabases($referencingDatabases)
  {
    $this->referencingDatabases = $referencingDatabases;
  }
  /**
   * @return string[]
   */
  public function getReferencingDatabases()
  {
    return $this->referencingDatabases;
  }
  /**
   * Output only. The current instance partition state.
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
   * Output only. The time at which the instance partition was most recently
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
class_alias(InstancePartition::class, 'Google_Service_Spanner_InstancePartition');
