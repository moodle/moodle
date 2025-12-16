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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ProvisionOrganizationRequest extends \Google\Model
{
  /**
   * Primary Cloud Platform region for analytics data storage. For valid values,
   * see [Create an
   * organization](https://cloud.google.com/apigee/docs/hybrid/latest/precog-
   * provision). Defaults to `us-west1`.
   *
   * @var string
   */
  public $analyticsRegion;
  /**
   * Compute Engine network used for Service Networking to be peered with Apigee
   * runtime instances. See [Getting started with the Service Networking
   * API](https://cloud.google.com/service-infrastructure/docs/service-
   * networking/getting-started). Apigee also supports shared VPC (that is, the
   * host network project is not the same as the one that is peering with
   * Apigee). See [Shared VPC
   * overview](https://cloud.google.com/vpc/docs/shared-vpc). To use a shared
   * VPC network, use the following format: `projects/{host-project-
   * id}/{region}/networks/{network-name}`. For example: `projects/my-sharedvpc-
   * host/global/networks/mynetwork`
   *
   * @var string
   */
  public $authorizedNetwork;
  /**
   * Optional. Flag that specifies whether the VPC Peering through Private
   * Google Access should be disabled between the consumer network and Apigee.
   * Required if an authorizedNetwork on the consumer project is not provided,
   * in which case the flag should be set to true. The value must be set before
   * the creation of any Apigee runtime instance and can be updated only when
   * there are no runtime instances. **Note:** Apigee will be deprecating the
   * vpc peering model that requires you to provide 'authorizedNetwork', by
   * making the non-peering model as the default way of provisioning Apigee
   * organization in future. So, this will be a temporary flag to enable the
   * transition. Not supported for Apigee hybrid.
   *
   * @var bool
   */
  public $disableVpcPeering;
  /**
   * Cloud Platform location for the runtime instance. Defaults to zone `us-
   * west1-a`. If a region is provided, `EVAL` organizations will use the region
   * for automatically selecting a zone for the runtime instance.
   *
   * @var string
   */
  public $runtimeLocation;

  /**
   * Primary Cloud Platform region for analytics data storage. For valid values,
   * see [Create an
   * organization](https://cloud.google.com/apigee/docs/hybrid/latest/precog-
   * provision). Defaults to `us-west1`.
   *
   * @param string $analyticsRegion
   */
  public function setAnalyticsRegion($analyticsRegion)
  {
    $this->analyticsRegion = $analyticsRegion;
  }
  /**
   * @return string
   */
  public function getAnalyticsRegion()
  {
    return $this->analyticsRegion;
  }
  /**
   * Compute Engine network used for Service Networking to be peered with Apigee
   * runtime instances. See [Getting started with the Service Networking
   * API](https://cloud.google.com/service-infrastructure/docs/service-
   * networking/getting-started). Apigee also supports shared VPC (that is, the
   * host network project is not the same as the one that is peering with
   * Apigee). See [Shared VPC
   * overview](https://cloud.google.com/vpc/docs/shared-vpc). To use a shared
   * VPC network, use the following format: `projects/{host-project-
   * id}/{region}/networks/{network-name}`. For example: `projects/my-sharedvpc-
   * host/global/networks/mynetwork`
   *
   * @param string $authorizedNetwork
   */
  public function setAuthorizedNetwork($authorizedNetwork)
  {
    $this->authorizedNetwork = $authorizedNetwork;
  }
  /**
   * @return string
   */
  public function getAuthorizedNetwork()
  {
    return $this->authorizedNetwork;
  }
  /**
   * Optional. Flag that specifies whether the VPC Peering through Private
   * Google Access should be disabled between the consumer network and Apigee.
   * Required if an authorizedNetwork on the consumer project is not provided,
   * in which case the flag should be set to true. The value must be set before
   * the creation of any Apigee runtime instance and can be updated only when
   * there are no runtime instances. **Note:** Apigee will be deprecating the
   * vpc peering model that requires you to provide 'authorizedNetwork', by
   * making the non-peering model as the default way of provisioning Apigee
   * organization in future. So, this will be a temporary flag to enable the
   * transition. Not supported for Apigee hybrid.
   *
   * @param bool $disableVpcPeering
   */
  public function setDisableVpcPeering($disableVpcPeering)
  {
    $this->disableVpcPeering = $disableVpcPeering;
  }
  /**
   * @return bool
   */
  public function getDisableVpcPeering()
  {
    return $this->disableVpcPeering;
  }
  /**
   * Cloud Platform location for the runtime instance. Defaults to zone `us-
   * west1-a`. If a region is provided, `EVAL` organizations will use the region
   * for automatically selecting a zone for the runtime instance.
   *
   * @param string $runtimeLocation
   */
  public function setRuntimeLocation($runtimeLocation)
  {
    $this->runtimeLocation = $runtimeLocation;
  }
  /**
   * @return string
   */
  public function getRuntimeLocation()
  {
    return $this->runtimeLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ProvisionOrganizationRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ProvisionOrganizationRequest');
