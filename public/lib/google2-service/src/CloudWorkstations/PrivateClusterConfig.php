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

namespace Google\Service\CloudWorkstations;

class PrivateClusterConfig extends \Google\Collection
{
  protected $collection_key = 'allowedProjects';
  /**
   * Optional. Additional projects that are allowed to attach to the workstation
   * cluster's service attachment. By default, the workstation cluster's project
   * and the VPC host project (if different) are allowed.
   *
   * @var string[]
   */
  public $allowedProjects;
  /**
   * Output only. Hostname for the workstation cluster. This field will be
   * populated only when private endpoint is enabled. To access workstations in
   * the workstation cluster, create a new DNS zone mapping this domain name to
   * an internal IP address and a forwarding rule mapping that address to the
   * service attachment.
   *
   * @var string
   */
  public $clusterHostname;
  /**
   * Immutable. Whether Workstations endpoint is private.
   *
   * @var bool
   */
  public $enablePrivateEndpoint;
  /**
   * Output only. Service attachment URI for the workstation cluster. The
   * service attachment is created when private endpoint is enabled. To access
   * workstations in the workstation cluster, configure access to the managed
   * service using [Private Service
   * Connect](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-services).
   *
   * @var string
   */
  public $serviceAttachmentUri;

  /**
   * Optional. Additional projects that are allowed to attach to the workstation
   * cluster's service attachment. By default, the workstation cluster's project
   * and the VPC host project (if different) are allowed.
   *
   * @param string[] $allowedProjects
   */
  public function setAllowedProjects($allowedProjects)
  {
    $this->allowedProjects = $allowedProjects;
  }
  /**
   * @return string[]
   */
  public function getAllowedProjects()
  {
    return $this->allowedProjects;
  }
  /**
   * Output only. Hostname for the workstation cluster. This field will be
   * populated only when private endpoint is enabled. To access workstations in
   * the workstation cluster, create a new DNS zone mapping this domain name to
   * an internal IP address and a forwarding rule mapping that address to the
   * service attachment.
   *
   * @param string $clusterHostname
   */
  public function setClusterHostname($clusterHostname)
  {
    $this->clusterHostname = $clusterHostname;
  }
  /**
   * @return string
   */
  public function getClusterHostname()
  {
    return $this->clusterHostname;
  }
  /**
   * Immutable. Whether Workstations endpoint is private.
   *
   * @param bool $enablePrivateEndpoint
   */
  public function setEnablePrivateEndpoint($enablePrivateEndpoint)
  {
    $this->enablePrivateEndpoint = $enablePrivateEndpoint;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateEndpoint()
  {
    return $this->enablePrivateEndpoint;
  }
  /**
   * Output only. Service attachment URI for the workstation cluster. The
   * service attachment is created when private endpoint is enabled. To access
   * workstations in the workstation cluster, configure access to the managed
   * service using [Private Service
   * Connect](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-services).
   *
   * @param string $serviceAttachmentUri
   */
  public function setServiceAttachmentUri($serviceAttachmentUri)
  {
    $this->serviceAttachmentUri = $serviceAttachmentUri;
  }
  /**
   * @return string
   */
  public function getServiceAttachmentUri()
  {
    return $this->serviceAttachmentUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateClusterConfig::class, 'Google_Service_CloudWorkstations_PrivateClusterConfig');
