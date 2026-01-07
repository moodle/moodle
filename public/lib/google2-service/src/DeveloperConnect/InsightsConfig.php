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

namespace Google\Service\DeveloperConnect;

class InsightsConfig extends \Google\Collection
{
  /**
   * No state specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The InsightsConfig is pending application discovery/runtime discovery.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The initial discovery process is complete.
   */
  public const STATE_COMPLETE = 'COMPLETE';
  /**
   * The InsightsConfig is in an error state.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'runtimeConfigs';
  /**
   * Optional. User specified annotations. See
   * https://google.aip.dev/148#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Optional. The name of the App Hub Application. Format:
   * projects/{project}/locations/{location}/applications/{application}
   *
   * @var string
   */
  public $appHubApplication;
  protected $artifactConfigsType = ArtifactConfig::class;
  protected $artifactConfigsDataType = 'array';
  /**
   * Output only. Create timestamp.
   *
   * @var string
   */
  public $createTime;
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * Optional. Set of labels associated with an InsightsConfig.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the InsightsConfig. Format:
   * projects/{project}/locations/{location}/insightsConfigs/{insightsConfig}
   *
   * @var string
   */
  public $name;
  protected $projectsType = Projects::class;
  protected $projectsDataType = '';
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of InsightsConfig does not match the user's
   * intended state, and the service is actively updating the resource to
   * reconcile them. This can happen due to user-triggered updates or system
   * actions like failover or maintenance.
   *
   * @var bool
   */
  public $reconciling;
  protected $runtimeConfigsType = RuntimeConfig::class;
  protected $runtimeConfigsDataType = 'array';
  /**
   * Optional. Output only. The state of the InsightsConfig.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User specified annotations. See
   * https://google.aip.dev/148#annotations for more details such as format and
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
   * Optional. The name of the App Hub Application. Format:
   * projects/{project}/locations/{location}/applications/{application}
   *
   * @param string $appHubApplication
   */
  public function setAppHubApplication($appHubApplication)
  {
    $this->appHubApplication = $appHubApplication;
  }
  /**
   * @return string
   */
  public function getAppHubApplication()
  {
    return $this->appHubApplication;
  }
  /**
   * Optional. The artifact configurations of the artifacts that are deployed.
   *
   * @param ArtifactConfig[] $artifactConfigs
   */
  public function setArtifactConfigs($artifactConfigs)
  {
    $this->artifactConfigs = $artifactConfigs;
  }
  /**
   * @return ArtifactConfig[]
   */
  public function getArtifactConfigs()
  {
    return $this->artifactConfigs;
  }
  /**
   * Output only. Create timestamp.
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
   * Output only. Any errors that occurred while setting up the InsightsConfig.
   * Each error will be in the format: `field_name: error_message`, e.g.
   * GetAppHubApplication: Permission denied while getting App Hub application.
   * Please grant permissions to the P4SA.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Optional. Set of labels associated with an InsightsConfig.
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
   * Identifier. The name of the InsightsConfig. Format:
   * projects/{project}/locations/{location}/insightsConfigs/{insightsConfig}
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
   * Optional. The GCP projects to track with the InsightsConfig.
   *
   * @param Projects $projects
   */
  public function setProjects(Projects $projects)
  {
    $this->projects = $projects;
  }
  /**
   * @return Projects
   */
  public function getProjects()
  {
    return $this->projects;
  }
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of InsightsConfig does not match the user's
   * intended state, and the service is actively updating the resource to
   * reconcile them. This can happen due to user-triggered updates or system
   * actions like failover or maintenance.
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
   * Output only. The runtime configurations where the application is deployed.
   *
   * @param RuntimeConfig[] $runtimeConfigs
   */
  public function setRuntimeConfigs($runtimeConfigs)
  {
    $this->runtimeConfigs = $runtimeConfigs;
  }
  /**
   * @return RuntimeConfig[]
   */
  public function getRuntimeConfigs()
  {
    return $this->runtimeConfigs;
  }
  /**
   * Optional. Output only. The state of the InsightsConfig.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, COMPLETE, ERROR
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
   * Output only. Update timestamp.
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
class_alias(InsightsConfig::class, 'Google_Service_DeveloperConnect_InsightsConfig');
