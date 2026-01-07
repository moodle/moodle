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

class MeshIstio extends \Google\Model
{
  /**
   * Identifier for the mesh in which this Istio service is defined. Corresponds
   * to the mesh_uid metric label in Istio metrics.
   *
   * @var string
   */
  public $meshUid;
  /**
   * The name of the Istio service underlying this service. Corresponds to the
   * destination_service_name metric label in Istio metrics.
   *
   * @var string
   */
  public $serviceName;
  /**
   * The namespace of the Istio service underlying this service. Corresponds to
   * the destination_service_namespace metric label in Istio metrics.
   *
   * @var string
   */
  public $serviceNamespace;

  /**
   * Identifier for the mesh in which this Istio service is defined. Corresponds
   * to the mesh_uid metric label in Istio metrics.
   *
   * @param string $meshUid
   */
  public function setMeshUid($meshUid)
  {
    $this->meshUid = $meshUid;
  }
  /**
   * @return string
   */
  public function getMeshUid()
  {
    return $this->meshUid;
  }
  /**
   * The name of the Istio service underlying this service. Corresponds to the
   * destination_service_name metric label in Istio metrics.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * The namespace of the Istio service underlying this service. Corresponds to
   * the destination_service_namespace metric label in Istio metrics.
   *
   * @param string $serviceNamespace
   */
  public function setServiceNamespace($serviceNamespace)
  {
    $this->serviceNamespace = $serviceNamespace;
  }
  /**
   * @return string
   */
  public function getServiceNamespace()
  {
    return $this->serviceNamespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeshIstio::class, 'Google_Service_Monitoring_MeshIstio');
