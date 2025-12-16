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

namespace Google\Service\Networkconnectivity;

class RegionalEndpoint extends \Google\Model
{
  /**
   * An invalid type as the default case.
   */
  public const ACCESS_TYPE_ACCESS_TYPE_UNSPECIFIED = 'ACCESS_TYPE_UNSPECIFIED';
  /**
   * This regional endpoint is accessible from all regions.
   */
  public const ACCESS_TYPE_GLOBAL = 'GLOBAL';
  /**
   * This regional endpoint is only accessible from the same region where it
   * resides.
   */
  public const ACCESS_TYPE_REGIONAL = 'REGIONAL';
  /**
   * Required. The access type of this regional endpoint. This field is
   * reflected in the PSC Forwarding Rule configuration to enable global access.
   *
   * @var string
   */
  public $accessType;
  /**
   * Optional. The IP Address of the Regional Endpoint. When no address is
   * provided, an IP from the subnetwork is allocated. Use one of the following
   * formats: * IPv4 address as in `10.0.0.1` * Address resource URI as in
   * `projects/{project}/regions/{region}/addresses/{address_name}` for an IPv4
   * or IPv6 address.
   *
   * @var string
   */
  public $address;
  /**
   * Output only. Time when the RegionalEndpoint was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The literal IP address of the PSC Forwarding Rule created on
   * behalf of the customer. This field is deprecated. Use address instead.
   *
   * @deprecated
   * @var string
   */
  public $ipAddress;
  /**
   * User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The name of a RegionalEndpoint. Pattern: `projects/{project}/l
   * ocations/{location}/regionalEndpoints/^[-a-z0-9](?:[-a-z0-9]{0,44})[a-z0-
   * 9]$`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name of the VPC network for this private regional endpoint.
   * Format: `projects/{project}/global/networks/{network}`
   *
   * @var string
   */
  public $network;
  /**
   * Output only. The resource reference of the PSC Forwarding Rule created on
   * behalf of the customer. Format: `//compute.googleapis.com/projects/{project
   * }/regions/{region}/forwardingRules/{forwarding_rule_name}`
   *
   * @var string
   */
  public $pscForwardingRule;
  /**
   * Optional. The name of the subnetwork from which the IP address will be
   * allocated. Format:
   * `projects/{project}/regions/{region}/subnetworks/{subnetwork}`
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Required. The service endpoint this private regional endpoint connects to.
   * Format: `{apiname}.{region}.p.rep.googleapis.com` Example: "cloudkms.us-
   * central1.p.rep.googleapis.com".
   *
   * @var string
   */
  public $targetGoogleApi;
  /**
   * Output only. Time when the RegionalEndpoint was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The access type of this regional endpoint. This field is
   * reflected in the PSC Forwarding Rule configuration to enable global access.
   *
   * Accepted values: ACCESS_TYPE_UNSPECIFIED, GLOBAL, REGIONAL
   *
   * @param self::ACCESS_TYPE_* $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return self::ACCESS_TYPE_*
   */
  public function getAccessType()
  {
    return $this->accessType;
  }
  /**
   * Optional. The IP Address of the Regional Endpoint. When no address is
   * provided, an IP from the subnetwork is allocated. Use one of the following
   * formats: * IPv4 address as in `10.0.0.1` * Address resource URI as in
   * `projects/{project}/regions/{region}/addresses/{address_name}` for an IPv4
   * or IPv6 address.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Output only. Time when the RegionalEndpoint was created.
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
   * Optional. A description of this resource.
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
   * Output only. The literal IP address of the PSC Forwarding Rule created on
   * behalf of the customer. This field is deprecated. Use address instead.
   *
   * @deprecated
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * User-defined labels.
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
   * Output only. The name of a RegionalEndpoint. Pattern: `projects/{project}/l
   * ocations/{location}/regionalEndpoints/^[-a-z0-9](?:[-a-z0-9]{0,44})[a-z0-
   * 9]$`.
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
   * Optional. The name of the VPC network for this private regional endpoint.
   * Format: `projects/{project}/global/networks/{network}`
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Output only. The resource reference of the PSC Forwarding Rule created on
   * behalf of the customer. Format: `//compute.googleapis.com/projects/{project
   * }/regions/{region}/forwardingRules/{forwarding_rule_name}`
   *
   * @param string $pscForwardingRule
   */
  public function setPscForwardingRule($pscForwardingRule)
  {
    $this->pscForwardingRule = $pscForwardingRule;
  }
  /**
   * @return string
   */
  public function getPscForwardingRule()
  {
    return $this->pscForwardingRule;
  }
  /**
   * Optional. The name of the subnetwork from which the IP address will be
   * allocated. Format:
   * `projects/{project}/regions/{region}/subnetworks/{subnetwork}`
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Required. The service endpoint this private regional endpoint connects to.
   * Format: `{apiname}.{region}.p.rep.googleapis.com` Example: "cloudkms.us-
   * central1.p.rep.googleapis.com".
   *
   * @param string $targetGoogleApi
   */
  public function setTargetGoogleApi($targetGoogleApi)
  {
    $this->targetGoogleApi = $targetGoogleApi;
  }
  /**
   * @return string
   */
  public function getTargetGoogleApi()
  {
    return $this->targetGoogleApi;
  }
  /**
   * Output only. Time when the RegionalEndpoint was updated.
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
class_alias(RegionalEndpoint::class, 'Google_Service_Networkconnectivity_RegionalEndpoint');
