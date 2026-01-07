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

namespace Google\Service\NetworkSecurity;

class FirewallEndpoint extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Processing configuration updates.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Down or in an error state.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $collection_key = 'associations';
  /**
   * Output only. List of networks that are associated with this endpoint in the
   * local zone. This is a projection of the FirewallEndpointAssociations
   * pointing at this endpoint. A network will only appear in this list after
   * traffic routing is fully configured. Format:
   * projects/{project}/global/networks/{name}.
   *
   * @deprecated
   * @var string[]
   */
  public $associatedNetworks;
  protected $associationsType = FirewallEndpointAssociationReference::class;
  protected $associationsDataType = 'array';
  /**
   * Required. Project to bill on endpoint uptime usage.
   *
   * @var string
   */
  public $billingProjectId;
  /**
   * Output only. Create time stamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the firewall endpoint. Max length 2048 characters.
   *
   * @var string
   */
  public $description;
  protected $endpointSettingsType = FirewallEndpointEndpointSettings::class;
  protected $endpointSettingsDataType = '';
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. Name of resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Whether reconciling is in progress, recommended per
   * https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Current state of the endpoint.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. List of networks that are associated with this endpoint in the
   * local zone. This is a projection of the FirewallEndpointAssociations
   * pointing at this endpoint. A network will only appear in this list after
   * traffic routing is fully configured. Format:
   * projects/{project}/global/networks/{name}.
   *
   * @deprecated
   * @param string[] $associatedNetworks
   */
  public function setAssociatedNetworks($associatedNetworks)
  {
    $this->associatedNetworks = $associatedNetworks;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getAssociatedNetworks()
  {
    return $this->associatedNetworks;
  }
  /**
   * Output only. List of FirewallEndpointAssociations that are associated to
   * this endpoint. An association will only appear in this list after traffic
   * routing is fully configured.
   *
   * @param FirewallEndpointAssociationReference[] $associations
   */
  public function setAssociations($associations)
  {
    $this->associations = $associations;
  }
  /**
   * @return FirewallEndpointAssociationReference[]
   */
  public function getAssociations()
  {
    return $this->associations;
  }
  /**
   * Required. Project to bill on endpoint uptime usage.
   *
   * @param string $billingProjectId
   */
  public function setBillingProjectId($billingProjectId)
  {
    $this->billingProjectId = $billingProjectId;
  }
  /**
   * @return string
   */
  public function getBillingProjectId()
  {
    return $this->billingProjectId;
  }
  /**
   * Output only. Create time stamp.
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
   * Optional. Description of the firewall endpoint. Max length 2048 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Settings for the endpoint.
   *
   * @param FirewallEndpointEndpointSettings $endpointSettings
   */
  public function setEndpointSettings(FirewallEndpointEndpointSettings $endpointSettings)
  {
    $this->endpointSettings = $endpointSettings;
  }
  /**
   * @return FirewallEndpointEndpointSettings
   */
  public function getEndpointSettings()
  {
    return $this->endpointSettings;
  }
  /**
   * Optional. Labels as key value pairs
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
   * Immutable. Identifier. Name of resource.
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
   * Output only. Whether reconciling is in progress, recommended per
   * https://google.aip.dev/128.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Current state of the endpoint.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, INACTIVE
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
   * Output only. Update time stamp
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
class_alias(FirewallEndpoint::class, 'Google_Service_NetworkSecurity_FirewallEndpoint');
