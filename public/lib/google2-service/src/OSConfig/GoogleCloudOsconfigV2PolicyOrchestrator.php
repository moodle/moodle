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

namespace Google\Service\OSConfig;

class GoogleCloudOsconfigV2PolicyOrchestrator extends \Google\Model
{
  /**
   * Required. Action to be done by the orchestrator in
   * `projects/{project_id}/zones/{zone_id}` locations defined by the
   * `orchestration_scope`. Allowed values: - `UPSERT` - Orchestrator will
   * create or update target resources. - `DELETE` - Orchestrator will delete
   * target resources, if they exist
   *
   * @var string
   */
  public $action;
  /**
   * Output only. Timestamp when the policy orchestrator resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Freeform text describing the purpose of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. In form of * `organizations/{organization_id}/locati
   * ons/global/policyOrchestrators/{orchestrator_id}` * `folders/{folder_id}/lo
   * cations/global/policyOrchestrators/{orchestrator_id}` * `projects/{project_
   * id_or_number}/locations/global/policyOrchestrators/{orchestrator_id}`
   *
   * @var string
   */
  public $name;
  protected $orchestratedResourceType = GoogleCloudOsconfigV2OrchestratedResource::class;
  protected $orchestratedResourceDataType = '';
  protected $orchestrationScopeType = GoogleCloudOsconfigV2OrchestrationScope::class;
  protected $orchestrationScopeDataType = '';
  protected $orchestrationStateType = GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState::class;
  protected $orchestrationStateDataType = '';
  /**
   * Output only. Set to true, if the there are ongoing changes being applied by
   * the orchestrator.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Optional. State of the orchestrator. Can be updated to change orchestrator
   * behaviour. Allowed values: - `ACTIVE` - orchestrator is actively looking
   * for actions to be taken. - `STOPPED` - orchestrator won't make any changes.
   * Note: There might be more states added in the future. We use string here
   * instead of an enum, to avoid the need of propagating new states to all the
   * client code.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when the policy orchestrator resource was last
   * modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Action to be done by the orchestrator in
   * `projects/{project_id}/zones/{zone_id}` locations defined by the
   * `orchestration_scope`. Allowed values: - `UPSERT` - Orchestrator will
   * create or update target resources. - `DELETE` - Orchestrator will delete
   * target resources, if they exist
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Output only. Timestamp when the policy orchestrator resource was created.
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
   * Optional. Freeform text describing the purpose of the resource.
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
   * Output only. This checksum is computed by the server based on the value of
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
   * Optional. Labels as key value pairs
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
   * Immutable. Identifier. In form of * `organizations/{organization_id}/locati
   * ons/global/policyOrchestrators/{orchestrator_id}` * `folders/{folder_id}/lo
   * cations/global/policyOrchestrators/{orchestrator_id}` * `projects/{project_
   * id_or_number}/locations/global/policyOrchestrators/{orchestrator_id}`
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
   * Required. Resource to be orchestrated by the policy orchestrator.
   *
   * @param GoogleCloudOsconfigV2OrchestratedResource $orchestratedResource
   */
  public function setOrchestratedResource(GoogleCloudOsconfigV2OrchestratedResource $orchestratedResource)
  {
    $this->orchestratedResource = $orchestratedResource;
  }
  /**
   * @return GoogleCloudOsconfigV2OrchestratedResource
   */
  public function getOrchestratedResource()
  {
    return $this->orchestratedResource;
  }
  /**
   * Optional. Defines scope for the orchestration, in context of the enclosing
   * PolicyOrchestrator resource. Scope is expanded into a list of pairs, in
   * which the rollout action will take place. Expansion starts with a Folder
   * resource parenting the PolicyOrchestrator resource: - All the descendant
   * projects are listed. - List of project is cross joined with a list of all
   * available zones. - Resulting list of pairs is filtered according to the
   * selectors.
   *
   * @param GoogleCloudOsconfigV2OrchestrationScope $orchestrationScope
   */
  public function setOrchestrationScope(GoogleCloudOsconfigV2OrchestrationScope $orchestrationScope)
  {
    $this->orchestrationScope = $orchestrationScope;
  }
  /**
   * @return GoogleCloudOsconfigV2OrchestrationScope
   */
  public function getOrchestrationScope()
  {
    return $this->orchestrationScope;
  }
  /**
   * Output only. State of the orchestration.
   *
   * @param GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState $orchestrationState
   */
  public function setOrchestrationState(GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState $orchestrationState)
  {
    $this->orchestrationState = $orchestrationState;
  }
  /**
   * @return GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState
   */
  public function getOrchestrationState()
  {
    return $this->orchestrationState;
  }
  /**
   * Output only. Set to true, if the there are ongoing changes being applied by
   * the orchestrator.
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
   * Optional. State of the orchestrator. Can be updated to change orchestrator
   * behaviour. Allowed values: - `ACTIVE` - orchestrator is actively looking
   * for actions to be taken. - `STOPPED` - orchestrator won't make any changes.
   * Note: There might be more states added in the future. We use string here
   * instead of an enum, to avoid the need of propagating new states to all the
   * client code.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Timestamp when the policy orchestrator resource was last
   * modified.
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
class_alias(GoogleCloudOsconfigV2PolicyOrchestrator::class, 'Google_Service_OSConfig_GoogleCloudOsconfigV2PolicyOrchestrator');
