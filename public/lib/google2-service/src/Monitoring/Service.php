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

namespace Google\Service\Monitoring;

class Service extends \Google\Model
{
  protected $appEngineType = AppEngine::class;
  protected $appEngineDataType = '';
  protected $basicServiceType = BasicService::class;
  protected $basicServiceDataType = '';
  protected $cloudEndpointsType = CloudEndpoints::class;
  protected $cloudEndpointsDataType = '';
  protected $cloudRunType = CloudRun::class;
  protected $cloudRunDataType = '';
  protected $clusterIstioType = ClusterIstio::class;
  protected $clusterIstioDataType = '';
  protected $customType = Custom::class;
  protected $customDataType = '';
  /**
   * Name used for UI elements listing this Service.
   *
   * @var string
   */
  public $displayName;
  protected $gkeNamespaceType = GkeNamespace::class;
  protected $gkeNamespaceDataType = '';
  protected $gkeServiceType = GkeService::class;
  protected $gkeServiceDataType = '';
  protected $gkeWorkloadType = GkeWorkload::class;
  protected $gkeWorkloadDataType = '';
  protected $istioCanonicalServiceType = IstioCanonicalService::class;
  protected $istioCanonicalServiceDataType = '';
  protected $meshIstioType = MeshIstio::class;
  protected $meshIstioDataType = '';
  /**
   * Identifier. Resource name for this Service. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/services/[SERVICE_ID]
   *
   * @var string
   */
  public $name;
  protected $telemetryType = Telemetry::class;
  protected $telemetryDataType = '';
  /**
   * Labels which have been used to annotate the service. Label keys must start
   * with a letter. Label keys and values may contain lowercase letters,
   * numbers, underscores, and dashes. Label keys and values have a maximum
   * length of 63 characters, and must be less than 128 bytes in size. Up to 64
   * label entries may be stored. For labels which do not have a semantic value,
   * the empty string may be supplied for the label value.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * Type used for App Engine services.
   *
   * @param AppEngine $appEngine
   */
  public function setAppEngine(AppEngine $appEngine)
  {
    $this->appEngine = $appEngine;
  }
  /**
   * @return AppEngine
   */
  public function getAppEngine()
  {
    return $this->appEngine;
  }
  /**
   * Message that contains the service type and service labels of this service
   * if it is a basic service. Documentation and examples here
   * (https://cloud.google.com/stackdriver/docs/solutions/slo-
   * monitoring/api/api-structures#basic-svc-w-basic-sli).
   *
   * @param BasicService $basicService
   */
  public function setBasicService(BasicService $basicService)
  {
    $this->basicService = $basicService;
  }
  /**
   * @return BasicService
   */
  public function getBasicService()
  {
    return $this->basicService;
  }
  /**
   * Type used for Cloud Endpoints services.
   *
   * @param CloudEndpoints $cloudEndpoints
   */
  public function setCloudEndpoints(CloudEndpoints $cloudEndpoints)
  {
    $this->cloudEndpoints = $cloudEndpoints;
  }
  /**
   * @return CloudEndpoints
   */
  public function getCloudEndpoints()
  {
    return $this->cloudEndpoints;
  }
  /**
   * Type used for Cloud Run services.
   *
   * @param CloudRun $cloudRun
   */
  public function setCloudRun(CloudRun $cloudRun)
  {
    $this->cloudRun = $cloudRun;
  }
  /**
   * @return CloudRun
   */
  public function getCloudRun()
  {
    return $this->cloudRun;
  }
  /**
   * Type used for Istio services that live in a Kubernetes cluster.
   *
   * @param ClusterIstio $clusterIstio
   */
  public function setClusterIstio(ClusterIstio $clusterIstio)
  {
    $this->clusterIstio = $clusterIstio;
  }
  /**
   * @return ClusterIstio
   */
  public function getClusterIstio()
  {
    return $this->clusterIstio;
  }
  /**
   * Custom service type.
   *
   * @param Custom $custom
   */
  public function setCustom(Custom $custom)
  {
    $this->custom = $custom;
  }
  /**
   * @return Custom
   */
  public function getCustom()
  {
    return $this->custom;
  }
  /**
   * Name used for UI elements listing this Service.
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
   * Type used for GKE Namespaces.
   *
   * @param GkeNamespace $gkeNamespace
   */
  public function setGkeNamespace(GkeNamespace $gkeNamespace)
  {
    $this->gkeNamespace = $gkeNamespace;
  }
  /**
   * @return GkeNamespace
   */
  public function getGkeNamespace()
  {
    return $this->gkeNamespace;
  }
  /**
   * Type used for GKE Services (the Kubernetes concept of a service).
   *
   * @param GkeService $gkeService
   */
  public function setGkeService(GkeService $gkeService)
  {
    $this->gkeService = $gkeService;
  }
  /**
   * @return GkeService
   */
  public function getGkeService()
  {
    return $this->gkeService;
  }
  /**
   * Type used for GKE Workloads.
   *
   * @param GkeWorkload $gkeWorkload
   */
  public function setGkeWorkload(GkeWorkload $gkeWorkload)
  {
    $this->gkeWorkload = $gkeWorkload;
  }
  /**
   * @return GkeWorkload
   */
  public function getGkeWorkload()
  {
    return $this->gkeWorkload;
  }
  /**
   * Type used for canonical services scoped to an Istio mesh. Metrics for Istio
   * are documented here
   * (https://istio.io/latest/docs/reference/config/metrics/)
   *
   * @param IstioCanonicalService $istioCanonicalService
   */
  public function setIstioCanonicalService(IstioCanonicalService $istioCanonicalService)
  {
    $this->istioCanonicalService = $istioCanonicalService;
  }
  /**
   * @return IstioCanonicalService
   */
  public function getIstioCanonicalService()
  {
    return $this->istioCanonicalService;
  }
  /**
   * Type used for Istio services scoped to an Istio mesh.
   *
   * @param MeshIstio $meshIstio
   */
  public function setMeshIstio(MeshIstio $meshIstio)
  {
    $this->meshIstio = $meshIstio;
  }
  /**
   * @return MeshIstio
   */
  public function getMeshIstio()
  {
    return $this->meshIstio;
  }
  /**
   * Identifier. Resource name for this Service. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/services/[SERVICE_ID]
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
   * Configuration for how to query telemetry on a Service.
   *
   * @param Telemetry $telemetry
   */
  public function setTelemetry(Telemetry $telemetry)
  {
    $this->telemetry = $telemetry;
  }
  /**
   * @return Telemetry
   */
  public function getTelemetry()
  {
    return $this->telemetry;
  }
  /**
   * Labels which have been used to annotate the service. Label keys must start
   * with a letter. Label keys and values may contain lowercase letters,
   * numbers, underscores, and dashes. Label keys and values have a maximum
   * length of 63 characters, and must be less than 128 bytes in size. Up to 64
   * label entries may be stored. For labels which do not have a semantic value,
   * the empty string may be supplied for the label value.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_Monitoring_Service');
