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

namespace Google\Service\GKEHub;

class ServiceMeshState extends \Google\Collection
{
  protected $collection_key = 'conditions';
  protected $analysisMessagesType = ServiceMeshAnalysisMessage::class;
  protected $analysisMessagesDataType = 'array';
  protected $conditionsType = ServiceMeshCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * The API version (i.e. Istio CRD version) for configuring service mesh in
   * this cluster. This version is influenced by the `default_channel` field.
   *
   * @var string
   */
  public $configApiVersion;
  protected $controlPlaneManagementType = ServiceMeshControlPlaneManagement::class;
  protected $controlPlaneManagementDataType = '';
  protected $dataPlaneManagementType = ServiceMeshDataPlaneManagement::class;
  protected $dataPlaneManagementDataType = '';

  /**
   * Output only. Results of running Service Mesh analyzers.
   *
   * @param ServiceMeshAnalysisMessage[] $analysisMessages
   */
  public function setAnalysisMessages($analysisMessages)
  {
    $this->analysisMessages = $analysisMessages;
  }
  /**
   * @return ServiceMeshAnalysisMessage[]
   */
  public function getAnalysisMessages()
  {
    return $this->analysisMessages;
  }
  /**
   * Output only. List of conditions reported for this membership.
   *
   * @param ServiceMeshCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return ServiceMeshCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * The API version (i.e. Istio CRD version) for configuring service mesh in
   * this cluster. This version is influenced by the `default_channel` field.
   *
   * @param string $configApiVersion
   */
  public function setConfigApiVersion($configApiVersion)
  {
    $this->configApiVersion = $configApiVersion;
  }
  /**
   * @return string
   */
  public function getConfigApiVersion()
  {
    return $this->configApiVersion;
  }
  /**
   * Output only. Status of control plane management
   *
   * @param ServiceMeshControlPlaneManagement $controlPlaneManagement
   */
  public function setControlPlaneManagement(ServiceMeshControlPlaneManagement $controlPlaneManagement)
  {
    $this->controlPlaneManagement = $controlPlaneManagement;
  }
  /**
   * @return ServiceMeshControlPlaneManagement
   */
  public function getControlPlaneManagement()
  {
    return $this->controlPlaneManagement;
  }
  /**
   * Output only. Status of data plane management.
   *
   * @param ServiceMeshDataPlaneManagement $dataPlaneManagement
   */
  public function setDataPlaneManagement(ServiceMeshDataPlaneManagement $dataPlaneManagement)
  {
    $this->dataPlaneManagement = $dataPlaneManagement;
  }
  /**
   * @return ServiceMeshDataPlaneManagement
   */
  public function getDataPlaneManagement()
  {
    return $this->dataPlaneManagement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshState::class, 'Google_Service_GKEHub_ServiceMeshState');
