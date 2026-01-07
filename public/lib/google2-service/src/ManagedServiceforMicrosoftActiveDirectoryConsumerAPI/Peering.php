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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class Peering extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Peering is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Peering is connected.
   */
  public const STATE_CONNECTED = 'CONNECTED';
  /**
   * Peering is disconnected.
   */
  public const STATE_DISCONNECTED = 'DISCONNECTED';
  /**
   * Peering is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Required. The full names of the Google Compute Engine
   * [networks](/compute/docs/networks-and-firewalls#networks) to which the
   * instance is connected. Caller needs to make sure that CIDR subnets do not
   * overlap between networks, else peering creation will fail.
   *
   * @var string
   */
  public $authorizedNetwork;
  /**
   * Output only. The time the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Full domain resource path for the Managed AD Domain involved in
   * peering. The resource path should be in the form:
   * `projects/{project_id}/locations/global/domains/{domain_name}`
   *
   * @var string
   */
  public $domainResource;
  /**
   * Optional. Resource labels to represent user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Unique name of the peering in this scope including projects
   * and location using the form:
   * `projects/{project_id}/locations/global/peerings/{peering_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of this Peering.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the current status of this
   * peering, if available.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Output only. Last update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The full names of the Google Compute Engine
   * [networks](/compute/docs/networks-and-firewalls#networks) to which the
   * instance is connected. Caller needs to make sure that CIDR subnets do not
   * overlap between networks, else peering creation will fail.
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
   * Output only. The time the instance was created.
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
   * Required. Full domain resource path for the Managed AD Domain involved in
   * peering. The resource path should be in the form:
   * `projects/{project_id}/locations/global/domains/{domain_name}`
   *
   * @param string $domainResource
   */
  public function setDomainResource($domainResource)
  {
    $this->domainResource = $domainResource;
  }
  /**
   * @return string
   */
  public function getDomainResource()
  {
    return $this->domainResource;
  }
  /**
   * Optional. Resource labels to represent user-provided metadata.
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
   * Output only. Unique name of the peering in this scope including projects
   * and location using the form:
   * `projects/{project_id}/locations/global/peerings/{peering_id}`.
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
   * Output only. The current state of this Peering.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, CONNECTED, DISCONNECTED,
   * DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Additional information about the current status of this
   * peering, if available.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Last update time.
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
class_alias(Peering::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_Peering');
