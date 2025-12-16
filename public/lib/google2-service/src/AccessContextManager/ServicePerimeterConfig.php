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

namespace Google\Service\AccessContextManager;

class ServicePerimeterConfig extends \Google\Collection
{
  protected $collection_key = 'restrictedServices';
  /**
   * A list of `AccessLevel` resource names that allow resources within the
   * `ServicePerimeter` to be accessed from the internet. `AccessLevels` listed
   * must be in the same policy as this `ServicePerimeter`. Referencing a
   * nonexistent `AccessLevel` is a syntax error. If no `AccessLevel` names are
   * listed, resources within the perimeter can only be accessed via Google
   * Cloud calls with request origins within the perimeter. Example:
   * `"accessPolicies/MY_POLICY/accessLevels/MY_LEVEL"`. For Service Perimeter
   * Bridge, must be empty.
   *
   * @var string[]
   */
  public $accessLevels;
  protected $egressPoliciesType = EgressPolicy::class;
  protected $egressPoliciesDataType = 'array';
  protected $ingressPoliciesType = IngressPolicy::class;
  protected $ingressPoliciesDataType = 'array';
  /**
   * A list of Google Cloud resources that are inside of the service perimeter.
   * Currently only projects and VPCs are allowed. Project format:
   * `projects/{project_number}` VPC network format:
   * `//compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NAME}`.
   *
   * @var string[]
   */
  public $resources;
  /**
   * Google Cloud services that are subject to the Service Perimeter
   * restrictions. For example, if `storage.googleapis.com` is specified, access
   * to the storage buckets inside the perimeter must meet the perimeter's
   * access restrictions.
   *
   * @var string[]
   */
  public $restrictedServices;
  protected $vpcAccessibleServicesType = VpcAccessibleServices::class;
  protected $vpcAccessibleServicesDataType = '';

  /**
   * A list of `AccessLevel` resource names that allow resources within the
   * `ServicePerimeter` to be accessed from the internet. `AccessLevels` listed
   * must be in the same policy as this `ServicePerimeter`. Referencing a
   * nonexistent `AccessLevel` is a syntax error. If no `AccessLevel` names are
   * listed, resources within the perimeter can only be accessed via Google
   * Cloud calls with request origins within the perimeter. Example:
   * `"accessPolicies/MY_POLICY/accessLevels/MY_LEVEL"`. For Service Perimeter
   * Bridge, must be empty.
   *
   * @param string[] $accessLevels
   */
  public function setAccessLevels($accessLevels)
  {
    $this->accessLevels = $accessLevels;
  }
  /**
   * @return string[]
   */
  public function getAccessLevels()
  {
    return $this->accessLevels;
  }
  /**
   * List of EgressPolicies to apply to the perimeter. A perimeter may have
   * multiple EgressPolicies, each of which is evaluated separately. Access is
   * granted if any EgressPolicy grants it. Must be empty for a perimeter
   * bridge.
   *
   * @param EgressPolicy[] $egressPolicies
   */
  public function setEgressPolicies($egressPolicies)
  {
    $this->egressPolicies = $egressPolicies;
  }
  /**
   * @return EgressPolicy[]
   */
  public function getEgressPolicies()
  {
    return $this->egressPolicies;
  }
  /**
   * List of IngressPolicies to apply to the perimeter. A perimeter may have
   * multiple IngressPolicies, each of which is evaluated separately. Access is
   * granted if any Ingress Policy grants it. Must be empty for a perimeter
   * bridge.
   *
   * @param IngressPolicy[] $ingressPolicies
   */
  public function setIngressPolicies($ingressPolicies)
  {
    $this->ingressPolicies = $ingressPolicies;
  }
  /**
   * @return IngressPolicy[]
   */
  public function getIngressPolicies()
  {
    return $this->ingressPolicies;
  }
  /**
   * A list of Google Cloud resources that are inside of the service perimeter.
   * Currently only projects and VPCs are allowed. Project format:
   * `projects/{project_number}` VPC network format:
   * `//compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NAME}`.
   *
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Google Cloud services that are subject to the Service Perimeter
   * restrictions. For example, if `storage.googleapis.com` is specified, access
   * to the storage buckets inside the perimeter must meet the perimeter's
   * access restrictions.
   *
   * @param string[] $restrictedServices
   */
  public function setRestrictedServices($restrictedServices)
  {
    $this->restrictedServices = $restrictedServices;
  }
  /**
   * @return string[]
   */
  public function getRestrictedServices()
  {
    return $this->restrictedServices;
  }
  /**
   * Configuration for APIs allowed within Perimeter.
   *
   * @param VpcAccessibleServices $vpcAccessibleServices
   */
  public function setVpcAccessibleServices(VpcAccessibleServices $vpcAccessibleServices)
  {
    $this->vpcAccessibleServices = $vpcAccessibleServices;
  }
  /**
   * @return VpcAccessibleServices
   */
  public function getVpcAccessibleServices()
  {
    return $this->vpcAccessibleServices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServicePerimeterConfig::class, 'Google_Service_AccessContextManager_ServicePerimeterConfig');
