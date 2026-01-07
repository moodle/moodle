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

namespace Google\Service\CloudDeploy;

class Target extends \Google\Collection
{
  protected $collection_key = 'executionConfigs';
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  protected $anthosClusterType = AnthosCluster::class;
  protected $anthosClusterDataType = '';
  protected $associatedEntitiesType = AssociatedEntities::class;
  protected $associatedEntitiesDataType = 'map';
  /**
   * Output only. Time at which the `Target` was created.
   *
   * @var string
   */
  public $createTime;
  protected $customTargetType = CustomTarget::class;
  protected $customTargetDataType = '';
  /**
   * Optional. The deploy parameters to use for this target.
   *
   * @var string[]
   */
  public $deployParameters;
  /**
   * Optional. Description of the `Target`. Max length is 255 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  protected $executionConfigsType = ExecutionConfig::class;
  protected $executionConfigsDataType = 'array';
  protected $gkeType = GkeCluster::class;
  protected $gkeDataType = '';
  /**
   * Optional. Labels are attributes that can be set and used by both the user
   * and by Cloud Deploy. Labels must meet the following constraints: * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
   *
   * @var string[]
   */
  public $labels;
  protected $multiTargetType = MultiTarget::class;
  protected $multiTargetDataType = '';
  /**
   * Identifier. Name of the `Target`. Format is
   * `projects/{project}/locations/{location}/targets/{target}`. The `target`
   * component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Whether or not the `Target` requires approval.
   *
   * @var bool
   */
  public $requireApproval;
  protected $runType = CloudRunLocation::class;
  protected $runDataType = '';
  /**
   * Output only. Resource id of the `Target`.
   *
   * @var string
   */
  public $targetId;
  /**
   * Output only. Unique identifier of the `Target`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Most recent time at which the `Target` was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Cloud Deploy. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
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
   * Optional. Information specifying an Anthos Cluster.
   *
   * @param AnthosCluster $anthosCluster
   */
  public function setAnthosCluster(AnthosCluster $anthosCluster)
  {
    $this->anthosCluster = $anthosCluster;
  }
  /**
   * @return AnthosCluster
   */
  public function getAnthosCluster()
  {
    return $this->anthosCluster;
  }
  /**
   * Optional. Map of entity IDs to their associated entities. Associated
   * entities allows specifying places other than the deployment target for
   * specific features. For example, the Gateway API canary can be configured to
   * deploy the HTTPRoute to a different cluster(s) than the deployment cluster
   * using associated entities. An entity ID must consist of lower-case letters,
   * numbers, and hyphens, start with a letter and end with a letter or a
   * number, and have a max length of 63 characters. In other words, it must
   * match the following regex: `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   *
   * @param AssociatedEntities[] $associatedEntities
   */
  public function setAssociatedEntities($associatedEntities)
  {
    $this->associatedEntities = $associatedEntities;
  }
  /**
   * @return AssociatedEntities[]
   */
  public function getAssociatedEntities()
  {
    return $this->associatedEntities;
  }
  /**
   * Output only. Time at which the `Target` was created.
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
   * Optional. Information specifying a Custom Target.
   *
   * @param CustomTarget $customTarget
   */
  public function setCustomTarget(CustomTarget $customTarget)
  {
    $this->customTarget = $customTarget;
  }
  /**
   * @return CustomTarget
   */
  public function getCustomTarget()
  {
    return $this->customTarget;
  }
  /**
   * Optional. The deploy parameters to use for this target.
   *
   * @param string[] $deployParameters
   */
  public function setDeployParameters($deployParameters)
  {
    $this->deployParameters = $deployParameters;
  }
  /**
   * @return string[]
   */
  public function getDeployParameters()
  {
    return $this->deployParameters;
  }
  /**
   * Optional. Description of the `Target`. Max length is 255 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
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
   * Optional. Configurations for all execution that relates to this `Target`.
   * Each `ExecutionEnvironmentUsage` value may only be used in a single
   * configuration; using the same value multiple times is an error. When one or
   * more configurations are specified, they must include the `RENDER` and
   * `DEPLOY` `ExecutionEnvironmentUsage` values. When no configurations are
   * specified, execution will use the default specified in `DefaultPool`.
   *
   * @param ExecutionConfig[] $executionConfigs
   */
  public function setExecutionConfigs($executionConfigs)
  {
    $this->executionConfigs = $executionConfigs;
  }
  /**
   * @return ExecutionConfig[]
   */
  public function getExecutionConfigs()
  {
    return $this->executionConfigs;
  }
  /**
   * Optional. Information specifying a GKE Cluster.
   *
   * @param GkeCluster $gke
   */
  public function setGke(GkeCluster $gke)
  {
    $this->gke = $gke;
  }
  /**
   * @return GkeCluster
   */
  public function getGke()
  {
    return $this->gke;
  }
  /**
   * Optional. Labels are attributes that can be set and used by both the user
   * and by Cloud Deploy. Labels must meet the following constraints: * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. * All characters must use UTF-8 encoding, and international
   * characters are allowed. * Keys must start with a lowercase letter or
   * international character. * Each resource is limited to a maximum of 64
   * labels. Both keys and values are additionally constrained to be <= 128
   * bytes.
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
   * Optional. Information specifying a multiTarget.
   *
   * @param MultiTarget $multiTarget
   */
  public function setMultiTarget(MultiTarget $multiTarget)
  {
    $this->multiTarget = $multiTarget;
  }
  /**
   * @return MultiTarget
   */
  public function getMultiTarget()
  {
    return $this->multiTarget;
  }
  /**
   * Identifier. Name of the `Target`. Format is
   * `projects/{project}/locations/{location}/targets/{target}`. The `target`
   * component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
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
   * Optional. Whether or not the `Target` requires approval.
   *
   * @param bool $requireApproval
   */
  public function setRequireApproval($requireApproval)
  {
    $this->requireApproval = $requireApproval;
  }
  /**
   * @return bool
   */
  public function getRequireApproval()
  {
    return $this->requireApproval;
  }
  /**
   * Optional. Information specifying a Cloud Run deployment target.
   *
   * @param CloudRunLocation $run
   */
  public function setRun(CloudRunLocation $run)
  {
    $this->run = $run;
  }
  /**
   * @return CloudRunLocation
   */
  public function getRun()
  {
    return $this->run;
  }
  /**
   * Output only. Resource id of the `Target`.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * Output only. Unique identifier of the `Target`.
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
   * Output only. Most recent time at which the `Target` was updated.
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
class_alias(Target::class, 'Google_Service_CloudDeploy_Target');
