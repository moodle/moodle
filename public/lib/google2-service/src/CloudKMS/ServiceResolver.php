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

namespace Google\Service\CloudKMS;

class ServiceResolver extends \Google\Collection
{
  protected $collection_key = 'serverCertificates';
  /**
   * Optional. The filter applied to the endpoints of the resolved service. If
   * no filter is specified, all endpoints will be considered. An endpoint will
   * be chosen arbitrarily from the filtered list for each request. For endpoint
   * filter syntax and examples, see https://cloud.google.com/service-directory/
   * docs/reference/rpc/google.cloud.servicedirectory.v1#resolveservicerequest.
   *
   * @var string
   */
  public $endpointFilter;
  /**
   * Required. The hostname of the EKM replica used at TLS and HTTP layers.
   *
   * @var string
   */
  public $hostname;
  protected $serverCertificatesType = Certificate::class;
  protected $serverCertificatesDataType = 'array';
  /**
   * Required. The resource name of the Service Directory service pointing to an
   * EKM replica, in the format `projects/locations/namespaces/services`.
   *
   * @var string
   */
  public $serviceDirectoryService;

  /**
   * Optional. The filter applied to the endpoints of the resolved service. If
   * no filter is specified, all endpoints will be considered. An endpoint will
   * be chosen arbitrarily from the filtered list for each request. For endpoint
   * filter syntax and examples, see https://cloud.google.com/service-directory/
   * docs/reference/rpc/google.cloud.servicedirectory.v1#resolveservicerequest.
   *
   * @param string $endpointFilter
   */
  public function setEndpointFilter($endpointFilter)
  {
    $this->endpointFilter = $endpointFilter;
  }
  /**
   * @return string
   */
  public function getEndpointFilter()
  {
    return $this->endpointFilter;
  }
  /**
   * Required. The hostname of the EKM replica used at TLS and HTTP layers.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Required. A list of leaf server certificates used to authenticate HTTPS
   * connections to the EKM replica. Currently, a maximum of 10 Certificate is
   * supported.
   *
   * @param Certificate[] $serverCertificates
   */
  public function setServerCertificates($serverCertificates)
  {
    $this->serverCertificates = $serverCertificates;
  }
  /**
   * @return Certificate[]
   */
  public function getServerCertificates()
  {
    return $this->serverCertificates;
  }
  /**
   * Required. The resource name of the Service Directory service pointing to an
   * EKM replica, in the format `projects/locations/namespaces/services`.
   *
   * @param string $serviceDirectoryService
   */
  public function setServiceDirectoryService($serviceDirectoryService)
  {
    $this->serviceDirectoryService = $serviceDirectoryService;
  }
  /**
   * @return string
   */
  public function getServiceDirectoryService()
  {
    return $this->serviceDirectoryService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceResolver::class, 'Google_Service_CloudKMS_ServiceResolver');
