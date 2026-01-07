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

class InstanceConfig extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const CONFIG_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Google-managed configuration.
   */
  public const CONFIG_TYPE_GOOGLE_MANAGED = 'GOOGLE_MANAGED';
  /**
   * User-managed configuration.
   */
  public const CONFIG_TYPE_USER_MANAGED = 'USER_MANAGED';
  /**
   * Not specified.
   */
  public const FREE_INSTANCE_AVAILABILITY_FREE_INSTANCE_AVAILABILITY_UNSPECIFIED = 'FREE_INSTANCE_AVAILABILITY_UNSPECIFIED';
  /**
   * Indicates that free instances are available to be created in this instance
   * configuration.
   */
  public const FREE_INSTANCE_AVAILABILITY_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that free instances are not supported in this instance
   * configuration.
   */
  public const FREE_INSTANCE_AVAILABILITY_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Indicates that free instances are currently not available to be created in
   * this instance configuration.
   */
  public const FREE_INSTANCE_AVAILABILITY_DISABLED = 'DISABLED';
  /**
   * Indicates that additional free instances cannot be created in this instance
   * configuration because the project has reached its limit of free instances.
   */
  public const FREE_INSTANCE_AVAILABILITY_QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
  /**
   * Quorum type not specified.
   */
  public const QUORUM_TYPE_QUORUM_TYPE_UNSPECIFIED = 'QUORUM_TYPE_UNSPECIFIED';
  /**
   * An instance configuration tagged with `REGION` quorum type forms a write
   * quorum in a single region.
   */
  public const QUORUM_TYPE_REGION = 'REGION';
  /**
   * An instance configuration tagged with the `DUAL_REGION` quorum type forms a
   * write quorum with exactly two read-write regions in a multi-region
   * configuration. This instance configuration requires failover in the event
   * of regional failures.
   */
  public const QUORUM_TYPE_DUAL_REGION = 'DUAL_REGION';
  /**
   * An instance configuration tagged with the `MULTI_REGION` quorum type forms
   * a write quorum from replicas that are spread across more than one region in
   * a multi-region configuration.
   */
  public const QUORUM_TYPE_MULTI_REGION = 'MULTI_REGION';
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance configuration is still being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance configuration is fully created and ready to be used to create
   * instances.
   */
  public const STATE_READY = 'READY';
  protected $collection_key = 'replicas';
  /**
   * Base configuration name, e.g. projects//instanceConfigs/nam3, based on
   * which this configuration is created. Only set for user-managed
   * configurations. `base_config` must refer to a configuration of type
   * `GOOGLE_MANAGED` in the same project as this configuration.
   *
   * @var string
   */
  public $baseConfig;
  /**
   * Output only. Whether this instance configuration is a Google-managed or
   * user-managed configuration.
   *
   * @var string
   */
  public $configType;
  /**
   * The name of this instance configuration as it appears in UIs.
   *
   * @var string
   */
  public $displayName;
  /**
   * etag is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a instance configuration from overwriting each
   * other. It is strongly suggested that systems make use of the etag in the
   * read-modify-write cycle to perform instance configuration updates in order
   * to avoid race conditions: An etag is returned in the response which
   * contains instance configurations, and systems are expected to put that etag
   * in the request to update instance configuration to ensure that their change
   * is applied to the same version of the instance configuration. If no etag is
   * provided in the call to update the instance configuration, then the
   * existing instance configuration is overwritten blindly.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Describes whether free instances are available to be created
   * in this instance configuration.
   *
   * @var string
   */
  public $freeInstanceAvailability;
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
   * characters may be allowed in the future. Therefore, you are advised to use
   * an internal label representation, such as JSON, which doesn't rely upon
   * specific characters being disallowed. For example, representing labels as
   * the string: name + "_" + value would prove problematic if we were to allow
   * "_" in a future release.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Allowed values of the "default_leader" schema option for databases in
   * instances that use this instance configuration.
   *
   * @var string[]
   */
  public $leaderOptions;
  /**
   * A unique identifier for the instance configuration. Values are of the form
   * `projects//instanceConfigs/a-z*`. User instance configuration must start
   * with `custom-`.
   *
   * @var string
   */
  public $name;
  protected $optionalReplicasType = ReplicaInfo::class;
  protected $optionalReplicasDataType = 'array';
  /**
   * Output only. The `QuorumType` of the instance configuration.
   *
   * @var string
   */
  public $quorumType;
  /**
   * Output only. If true, the instance configuration is being created or
   * updated. If false, there are no ongoing operations for the instance
   * configuration.
   *
   * @var bool
   */
  public $reconciling;
  protected $replicasType = ReplicaInfo::class;
  protected $replicasDataType = 'array';
  /**
   * Output only. The current instance configuration state. Applicable only for
   * `USER_MANAGED` configurations.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The storage limit in bytes per processing unit.
   *
   * @var string
   */
  public $storageLimitPerProcessingUnit;

  /**
   * Base configuration name, e.g. projects//instanceConfigs/nam3, based on
   * which this configuration is created. Only set for user-managed
   * configurations. `base_config` must refer to a configuration of type
   * `GOOGLE_MANAGED` in the same project as this configuration.
   *
   * @param string $baseConfig
   */
  public function setBaseConfig($baseConfig)
  {
    $this->baseConfig = $baseConfig;
  }
  /**
   * @return string
   */
  public function getBaseConfig()
  {
    return $this->baseConfig;
  }
  /**
   * Output only. Whether this instance configuration is a Google-managed or
   * user-managed configuration.
   *
   * Accepted values: TYPE_UNSPECIFIED, GOOGLE_MANAGED, USER_MANAGED
   *
   * @param self::CONFIG_TYPE_* $configType
   */
  public function setConfigType($configType)
  {
    $this->configType = $configType;
  }
  /**
   * @return self::CONFIG_TYPE_*
   */
  public function getConfigType()
  {
    return $this->configType;
  }
  /**
   * The name of this instance configuration as it appears in UIs.
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
   * etag is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a instance configuration from overwriting each
   * other. It is strongly suggested that systems make use of the etag in the
   * read-modify-write cycle to perform instance configuration updates in order
   * to avoid race conditions: An etag is returned in the response which
   * contains instance configurations, and systems are expected to put that etag
   * in the request to update instance configuration to ensure that their change
   * is applied to the same version of the instance configuration. If no etag is
   * provided in the call to update the instance configuration, then the
   * existing instance configuration is overwritten blindly.
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
   * Output only. Describes whether free instances are available to be created
   * in this instance configuration.
   *
   * Accepted values: FREE_INSTANCE_AVAILABILITY_UNSPECIFIED, AVAILABLE,
   * UNSUPPORTED, DISABLED, QUOTA_EXCEEDED
   *
   * @param self::FREE_INSTANCE_AVAILABILITY_* $freeInstanceAvailability
   */
  public function setFreeInstanceAvailability($freeInstanceAvailability)
  {
    $this->freeInstanceAvailability = $freeInstanceAvailability;
  }
  /**
   * @return self::FREE_INSTANCE_AVAILABILITY_*
   */
  public function getFreeInstanceAvailability()
  {
    return $this->freeInstanceAvailability;
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
   * characters may be allowed in the future. Therefore, you are advised to use
   * an internal label representation, such as JSON, which doesn't rely upon
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
   * Allowed values of the "default_leader" schema option for databases in
   * instances that use this instance configuration.
   *
   * @param string[] $leaderOptions
   */
  public function setLeaderOptions($leaderOptions)
  {
    $this->leaderOptions = $leaderOptions;
  }
  /**
   * @return string[]
   */
  public function getLeaderOptions()
  {
    return $this->leaderOptions;
  }
  /**
   * A unique identifier for the instance configuration. Values are of the form
   * `projects//instanceConfigs/a-z*`. User instance configuration must start
   * with `custom-`.
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
   * Output only. The available optional replicas to choose from for user-
   * managed configurations. Populated for Google-managed configurations.
   *
   * @param ReplicaInfo[] $optionalReplicas
   */
  public function setOptionalReplicas($optionalReplicas)
  {
    $this->optionalReplicas = $optionalReplicas;
  }
  /**
   * @return ReplicaInfo[]
   */
  public function getOptionalReplicas()
  {
    return $this->optionalReplicas;
  }
  /**
   * Output only. The `QuorumType` of the instance configuration.
   *
   * Accepted values: QUORUM_TYPE_UNSPECIFIED, REGION, DUAL_REGION, MULTI_REGION
   *
   * @param self::QUORUM_TYPE_* $quorumType
   */
  public function setQuorumType($quorumType)
  {
    $this->quorumType = $quorumType;
  }
  /**
   * @return self::QUORUM_TYPE_*
   */
  public function getQuorumType()
  {
    return $this->quorumType;
  }
  /**
   * Output only. If true, the instance configuration is being created or
   * updated. If false, there are no ongoing operations for the instance
   * configuration.
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
   * The geographic placement of nodes in this instance configuration and their
   * replication properties. To create user-managed configurations, input
   * `replicas` must include all replicas in `replicas` of the `base_config` and
   * include one or more replicas in the `optional_replicas` of the
   * `base_config`.
   *
   * @param ReplicaInfo[] $replicas
   */
  public function setReplicas($replicas)
  {
    $this->replicas = $replicas;
  }
  /**
   * @return ReplicaInfo[]
   */
  public function getReplicas()
  {
    return $this->replicas;
  }
  /**
   * Output only. The current instance configuration state. Applicable only for
   * `USER_MANAGED` configurations.
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
   * Output only. The storage limit in bytes per processing unit.
   *
   * @param string $storageLimitPerProcessingUnit
   */
  public function setStorageLimitPerProcessingUnit($storageLimitPerProcessingUnit)
  {
    $this->storageLimitPerProcessingUnit = $storageLimitPerProcessingUnit;
  }
  /**
   * @return string
   */
  public function getStorageLimitPerProcessingUnit()
  {
    return $this->storageLimitPerProcessingUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceConfig::class, 'Google_Service_Spanner_InstanceConfig');
