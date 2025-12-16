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

namespace Google\Service\Baremetalsolution;

class NetworkConfig extends \Google\Collection
{
  /**
   * Unspecified value.
   */
  public const BANDWIDTH_BANDWIDTH_UNSPECIFIED = 'BANDWIDTH_UNSPECIFIED';
  /**
   * 1 Gbps.
   */
  public const BANDWIDTH_BW_1_GBPS = 'BW_1_GBPS';
  /**
   * 2 Gbps.
   */
  public const BANDWIDTH_BW_2_GBPS = 'BW_2_GBPS';
  /**
   * 5 Gbps.
   */
  public const BANDWIDTH_BW_5_GBPS = 'BW_5_GBPS';
  /**
   * 10 Gbps.
   */
  public const BANDWIDTH_BW_10_GBPS = 'BW_10_GBPS';
  /**
   * Unspecified value.
   */
  public const SERVICE_CIDR_SERVICE_CIDR_UNSPECIFIED = 'SERVICE_CIDR_UNSPECIFIED';
  /**
   * Services are disabled for the given network.
   */
  public const SERVICE_CIDR_DISABLED = 'DISABLED';
  /**
   * Use the highest /26 block of the network to host services.
   */
  public const SERVICE_CIDR_HIGH_26 = 'HIGH_26';
  /**
   * Use the highest /27 block of the network to host services.
   */
  public const SERVICE_CIDR_HIGH_27 = 'HIGH_27';
  /**
   * Use the highest /28 block of the network to host services.
   */
  public const SERVICE_CIDR_HIGH_28 = 'HIGH_28';
  /**
   * Unspecified value.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Client network, that is a network peered to a GCP VPC.
   */
  public const TYPE_CLIENT = 'CLIENT';
  /**
   * Private network, that is a network local to the BMS POD.
   */
  public const TYPE_PRIVATE = 'PRIVATE';
  protected $collection_key = 'vlanAttachments';
  /**
   * Interconnect bandwidth. Set only when type is CLIENT.
   *
   * @var string
   */
  public $bandwidth;
  /**
   * CIDR range of the network.
   *
   * @var string
   */
  public $cidr;
  /**
   * The GCP service of the network. Available gcp_service are in
   * https://cloud.google.com/bare-metal/docs/bms-planning.
   *
   * @var string
   */
  public $gcpService;
  /**
   * A transient unique identifier to identify a volume within an
   * ProvisioningConfig request.
   *
   * @var string
   */
  public $id;
  /**
   * The JumboFramesEnabled option for customer to set.
   *
   * @var bool
   */
  public $jumboFramesEnabled;
  /**
   * Output only. The name of the network config.
   *
   * @var string
   */
  public $name;
  /**
   * Service CIDR, if any.
   *
   * @var string
   */
  public $serviceCidr;
  /**
   * The type of this network, either Client or Private.
   *
   * @var string
   */
  public $type;
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @var string
   */
  public $userNote;
  protected $vlanAttachmentsType = IntakeVlanAttachment::class;
  protected $vlanAttachmentsDataType = 'array';
  /**
   * Whether the VLAN attachment pair is located in the same project.
   *
   * @var bool
   */
  public $vlanSameProject;
  /**
   * Optional. The name of a pre-existing Vrf that the network should be
   * attached to. Format is `vrfs/{vrf}`. If vrf is specified, vlan_attachments
   * must be empty.
   *
   * @var string
   */
  public $vrf;

  /**
   * Interconnect bandwidth. Set only when type is CLIENT.
   *
   * Accepted values: BANDWIDTH_UNSPECIFIED, BW_1_GBPS, BW_2_GBPS, BW_5_GBPS,
   * BW_10_GBPS
   *
   * @param self::BANDWIDTH_* $bandwidth
   */
  public function setBandwidth($bandwidth)
  {
    $this->bandwidth = $bandwidth;
  }
  /**
   * @return self::BANDWIDTH_*
   */
  public function getBandwidth()
  {
    return $this->bandwidth;
  }
  /**
   * CIDR range of the network.
   *
   * @param string $cidr
   */
  public function setCidr($cidr)
  {
    $this->cidr = $cidr;
  }
  /**
   * @return string
   */
  public function getCidr()
  {
    return $this->cidr;
  }
  /**
   * The GCP service of the network. Available gcp_service are in
   * https://cloud.google.com/bare-metal/docs/bms-planning.
   *
   * @param string $gcpService
   */
  public function setGcpService($gcpService)
  {
    $this->gcpService = $gcpService;
  }
  /**
   * @return string
   */
  public function getGcpService()
  {
    return $this->gcpService;
  }
  /**
   * A transient unique identifier to identify a volume within an
   * ProvisioningConfig request.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The JumboFramesEnabled option for customer to set.
   *
   * @param bool $jumboFramesEnabled
   */
  public function setJumboFramesEnabled($jumboFramesEnabled)
  {
    $this->jumboFramesEnabled = $jumboFramesEnabled;
  }
  /**
   * @return bool
   */
  public function getJumboFramesEnabled()
  {
    return $this->jumboFramesEnabled;
  }
  /**
   * Output only. The name of the network config.
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
   * Service CIDR, if any.
   *
   * Accepted values: SERVICE_CIDR_UNSPECIFIED, DISABLED, HIGH_26, HIGH_27,
   * HIGH_28
   *
   * @param self::SERVICE_CIDR_* $serviceCidr
   */
  public function setServiceCidr($serviceCidr)
  {
    $this->serviceCidr = $serviceCidr;
  }
  /**
   * @return self::SERVICE_CIDR_*
   */
  public function getServiceCidr()
  {
    return $this->serviceCidr;
  }
  /**
   * The type of this network, either Client or Private.
   *
   * Accepted values: TYPE_UNSPECIFIED, CLIENT, PRIVATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @param string $userNote
   */
  public function setUserNote($userNote)
  {
    $this->userNote = $userNote;
  }
  /**
   * @return string
   */
  public function getUserNote()
  {
    return $this->userNote;
  }
  /**
   * List of VLAN attachments. As of now there are always 2 attachments, but it
   * is going to change in the future (multi vlan). Use only one of
   * vlan_attachments or vrf
   *
   * @param IntakeVlanAttachment[] $vlanAttachments
   */
  public function setVlanAttachments($vlanAttachments)
  {
    $this->vlanAttachments = $vlanAttachments;
  }
  /**
   * @return IntakeVlanAttachment[]
   */
  public function getVlanAttachments()
  {
    return $this->vlanAttachments;
  }
  /**
   * Whether the VLAN attachment pair is located in the same project.
   *
   * @param bool $vlanSameProject
   */
  public function setVlanSameProject($vlanSameProject)
  {
    $this->vlanSameProject = $vlanSameProject;
  }
  /**
   * @return bool
   */
  public function getVlanSameProject()
  {
    return $this->vlanSameProject;
  }
  /**
   * Optional. The name of a pre-existing Vrf that the network should be
   * attached to. Format is `vrfs/{vrf}`. If vrf is specified, vlan_attachments
   * must be empty.
   *
   * @param string $vrf
   */
  public function setVrf($vrf)
  {
    $this->vrf = $vrf;
  }
  /**
   * @return string
   */
  public function getVrf()
  {
    return $this->vrf;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_Baremetalsolution_NetworkConfig');
