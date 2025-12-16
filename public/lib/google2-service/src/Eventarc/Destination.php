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

namespace Google\Service\Eventarc;

class Destination extends \Google\Model
{
  /**
   * The Cloud Function resource name. Cloud Functions V1 and V2 are supported.
   * Format: `projects/{project}/locations/{location}/functions/{function}` This
   * is a read-only field. Creating Cloud Functions V1/V2 triggers is only
   * supported via the Cloud Functions product. An error will be returned if the
   * user sets this value.
   *
   * @var string
   */
  public $cloudFunction;
  protected $cloudRunType = CloudRun::class;
  protected $cloudRunDataType = '';
  protected $gkeType = GKE::class;
  protected $gkeDataType = '';
  protected $httpEndpointType = HttpEndpoint::class;
  protected $httpEndpointDataType = '';
  protected $networkConfigType = NetworkConfig::class;
  protected $networkConfigDataType = '';
  /**
   * The resource name of the Workflow whose Executions are triggered by the
   * events. The Workflow resource should be deployed in the same project as the
   * trigger. Format:
   * `projects/{project}/locations/{location}/workflows/{workflow}`
   *
   * @var string
   */
  public $workflow;

  /**
   * The Cloud Function resource name. Cloud Functions V1 and V2 are supported.
   * Format: `projects/{project}/locations/{location}/functions/{function}` This
   * is a read-only field. Creating Cloud Functions V1/V2 triggers is only
   * supported via the Cloud Functions product. An error will be returned if the
   * user sets this value.
   *
   * @param string $cloudFunction
   */
  public function setCloudFunction($cloudFunction)
  {
    $this->cloudFunction = $cloudFunction;
  }
  /**
   * @return string
   */
  public function getCloudFunction()
  {
    return $this->cloudFunction;
  }
  /**
   * Cloud Run fully-managed resource that receives the events. The resource
   * should be in the same project as the trigger.
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
   * A GKE service capable of receiving events. The service should be running in
   * the same project as the trigger.
   *
   * @param GKE $gke
   */
  public function setGke(GKE $gke)
  {
    $this->gke = $gke;
  }
  /**
   * @return GKE
   */
  public function getGke()
  {
    return $this->gke;
  }
  /**
   * An HTTP endpoint destination described by an URI.
   *
   * @param HttpEndpoint $httpEndpoint
   */
  public function setHttpEndpoint(HttpEndpoint $httpEndpoint)
  {
    $this->httpEndpoint = $httpEndpoint;
  }
  /**
   * @return HttpEndpoint
   */
  public function getHttpEndpoint()
  {
    return $this->httpEndpoint;
  }
  /**
   * Optional. Network config is used to configure how Eventarc resolves and
   * connect to a destination. This should only be used with HttpEndpoint
   * destination type.
   *
   * @param NetworkConfig $networkConfig
   */
  public function setNetworkConfig(NetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return NetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * The resource name of the Workflow whose Executions are triggered by the
   * events. The Workflow resource should be deployed in the same project as the
   * trigger. Format:
   * `projects/{project}/locations/{location}/workflows/{workflow}`
   *
   * @param string $workflow
   */
  public function setWorkflow($workflow)
  {
    $this->workflow = $workflow;
  }
  /**
   * @return string
   */
  public function getWorkflow()
  {
    return $this->workflow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Destination::class, 'Google_Service_Eventarc_Destination');
