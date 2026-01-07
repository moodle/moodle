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

namespace Google\Service\CloudAlloyDBAdmin;

class PscAutoConnectionConfig extends \Google\Model
{
  /**
   * The consumer network for the PSC service automation, example:
   * "projects/vpc-host-project/global/networks/default". The consumer network
   * might be hosted a different project than the consumer project.
   *
   * @var string
   */
  public $consumerNetwork;
  /**
   * Output only. The status of the service connection policy. Possible values:
   * "STATE_UNSPECIFIED" - Default state, when Connection Map is created
   * initially. "VALID" - Set when policy and map configuration is valid, and
   * their matching can lead to allowing creation of PSC Connections subject to
   * other constraints like connections limit. "CONNECTION_POLICY_MISSING" - No
   * Service Connection Policy found for this network and Service Class
   * "POLICY_LIMIT_REACHED" - Service Connection Policy limit reached for this
   * network and Service Class "CONSUMER_INSTANCE_PROJECT_NOT_ALLOWLISTED" - The
   * consumer instance project is not in
   * AllowedGoogleProducersResourceHierarchyLevels of the matching
   * ServiceConnectionPolicy.
   *
   * @var string
   */
  public $consumerNetworkStatus;
  /**
   * The consumer project to which the PSC service automation endpoint will be
   * created.
   *
   * @var string
   */
  public $consumerProject;
  /**
   * Output only. The IP address of the PSC service automation endpoint.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Output only. The status of the PSC service automation connection. Possible
   * values: "STATE_UNSPECIFIED" - An invalid state as the default case.
   * "ACTIVE" - The connection has been created successfully. "FAILED" - The
   * connection is not functional since some resources on the connection fail to
   * be created. "CREATING" - The connection is being created. "DELETING" - The
   * connection is being deleted. "CREATE_REPAIRING" - The connection is being
   * repaired to complete creation. "DELETE_REPAIRING" - The connection is being
   * repaired to complete deletion.
   *
   * @var string
   */
  public $status;

  /**
   * The consumer network for the PSC service automation, example:
   * "projects/vpc-host-project/global/networks/default". The consumer network
   * might be hosted a different project than the consumer project.
   *
   * @param string $consumerNetwork
   */
  public function setConsumerNetwork($consumerNetwork)
  {
    $this->consumerNetwork = $consumerNetwork;
  }
  /**
   * @return string
   */
  public function getConsumerNetwork()
  {
    return $this->consumerNetwork;
  }
  /**
   * Output only. The status of the service connection policy. Possible values:
   * "STATE_UNSPECIFIED" - Default state, when Connection Map is created
   * initially. "VALID" - Set when policy and map configuration is valid, and
   * their matching can lead to allowing creation of PSC Connections subject to
   * other constraints like connections limit. "CONNECTION_POLICY_MISSING" - No
   * Service Connection Policy found for this network and Service Class
   * "POLICY_LIMIT_REACHED" - Service Connection Policy limit reached for this
   * network and Service Class "CONSUMER_INSTANCE_PROJECT_NOT_ALLOWLISTED" - The
   * consumer instance project is not in
   * AllowedGoogleProducersResourceHierarchyLevels of the matching
   * ServiceConnectionPolicy.
   *
   * @param string $consumerNetworkStatus
   */
  public function setConsumerNetworkStatus($consumerNetworkStatus)
  {
    $this->consumerNetworkStatus = $consumerNetworkStatus;
  }
  /**
   * @return string
   */
  public function getConsumerNetworkStatus()
  {
    return $this->consumerNetworkStatus;
  }
  /**
   * The consumer project to which the PSC service automation endpoint will be
   * created.
   *
   * @param string $consumerProject
   */
  public function setConsumerProject($consumerProject)
  {
    $this->consumerProject = $consumerProject;
  }
  /**
   * @return string
   */
  public function getConsumerProject()
  {
    return $this->consumerProject;
  }
  /**
   * Output only. The IP address of the PSC service automation endpoint.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Output only. The status of the PSC service automation connection. Possible
   * values: "STATE_UNSPECIFIED" - An invalid state as the default case.
   * "ACTIVE" - The connection has been created successfully. "FAILED" - The
   * connection is not functional since some resources on the connection fail to
   * be created. "CREATING" - The connection is being created. "DELETING" - The
   * connection is being deleted. "CREATE_REPAIRING" - The connection is being
   * repaired to complete creation. "DELETE_REPAIRING" - The connection is being
   * repaired to complete deletion.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscAutoConnectionConfig::class, 'Google_Service_CloudAlloyDBAdmin_PscAutoConnectionConfig');
