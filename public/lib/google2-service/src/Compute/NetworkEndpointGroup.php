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

namespace Google\Service\Compute;

class NetworkEndpointGroup extends \Google\Model
{
  /**
   * The network endpoint is represented by an IP address.
   */
  public const NETWORK_ENDPOINT_TYPE_GCE_VM_IP = 'GCE_VM_IP';
  /**
   * The network endpoint is represented by IP address and port pair.
   */
  public const NETWORK_ENDPOINT_TYPE_GCE_VM_IP_PORT = 'GCE_VM_IP_PORT';
  /**
   * The network endpoint is represented by an IP, Port and Client Destination
   * Port.
   */
  public const NETWORK_ENDPOINT_TYPE_GCE_VM_IP_PORTMAP = 'GCE_VM_IP_PORTMAP';
  /**
   * The network endpoint is represented by fully qualified domain name and
   * port.
   */
  public const NETWORK_ENDPOINT_TYPE_INTERNET_FQDN_PORT = 'INTERNET_FQDN_PORT';
  /**
   * The network endpoint is represented by an internet IP address and port.
   */
  public const NETWORK_ENDPOINT_TYPE_INTERNET_IP_PORT = 'INTERNET_IP_PORT';
  /**
   * The network endpoint is represented by an IP address and port. The endpoint
   * belongs to a VM or pod running in a customer's on-premises.
   */
  public const NETWORK_ENDPOINT_TYPE_NON_GCP_PRIVATE_IP_PORT = 'NON_GCP_PRIVATE_IP_PORT';
  /**
   * The network endpoint is either public Google APIs or services exposed by
   * other GCP Project with a Service Attachment. The connection is set up by
   * private service connect
   */
  public const NETWORK_ENDPOINT_TYPE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * The network endpoint is handled by specified serverless infrastructure.
   */
  public const NETWORK_ENDPOINT_TYPE_SERVERLESS = 'SERVERLESS';
  /**
   * Optional. Metadata defined as annotations on the network endpoint group.
   *
   * @var string[]
   */
  public $annotations;
  protected $appEngineType = NetworkEndpointGroupAppEngine::class;
  protected $appEngineDataType = '';
  protected $cloudFunctionType = NetworkEndpointGroupCloudFunction::class;
  protected $cloudFunctionDataType = '';
  protected $cloudRunType = NetworkEndpointGroupCloudRun::class;
  protected $cloudRunDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * The default port used if the port number is not specified in the network
   * endpoint.
   *
   * Optional. If the network endpoint type is either GCE_VM_IP,SERVERLESS or
   * PRIVATE_SERVICE_CONNECT, this field must not be specified.
   *
   * @var int
   */
  public $defaultPort;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#networkEndpointGroup for network endpoint group.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * The URL of the network to which all network endpoints in the NEG belong.
   * Uses default project network if unspecified.
   *
   * @var string
   */
  public $network;
  /**
   * Type of network endpoints in this network endpoint group. Can be one
   * ofGCE_VM_IP, GCE_VM_IP_PORT,NON_GCP_PRIVATE_IP_PORT,
   * INTERNET_FQDN_PORT,INTERNET_IP_PORT, SERVERLESS,PRIVATE_SERVICE_CONNECT,
   * GCE_VM_IP_PORTMAP.
   *
   * @var string
   */
  public $networkEndpointType;
  protected $pscDataType = NetworkEndpointGroupPscData::class;
  protected $pscDataDataType = '';
  /**
   * The target service url used to set up private service connection to a
   * Google API or a PSC Producer Service Attachment. An example value is: asia-
   * northeast3-cloudkms.googleapis.com.
   *
   * Optional. Only valid when networkEndpointType isPRIVATE_SERVICE_CONNECT.
   *
   * @var string
   */
  public $pscTargetService;
  /**
   * Output only. [Output Only] The URL of theregion where the network endpoint
   * group is located.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output only] Number of network endpoints in the network
   * endpoint group.
   *
   * @var int
   */
  public $size;
  /**
   * Optional URL of the subnetwork to which all network endpoints in the NEG
   * belong.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Output only. [Output Only] The URL of thezone where the network endpoint
   * group is located.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Metadata defined as annotations on the network endpoint group.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Optional. Only valid when networkEndpointType isSERVERLESS. Only one of
   * cloudRun,appEngine or cloudFunction may be set.
   *
   * @param NetworkEndpointGroupAppEngine $appEngine
   */
  public function setAppEngine(NetworkEndpointGroupAppEngine $appEngine)
  {
    $this->appEngine = $appEngine;
  }
  /**
   * @return NetworkEndpointGroupAppEngine
   */
  public function getAppEngine()
  {
    return $this->appEngine;
  }
  /**
   * Optional. Only valid when networkEndpointType isSERVERLESS. Only one of
   * cloudRun,appEngine or cloudFunction may be set.
   *
   * @param NetworkEndpointGroupCloudFunction $cloudFunction
   */
  public function setCloudFunction(NetworkEndpointGroupCloudFunction $cloudFunction)
  {
    $this->cloudFunction = $cloudFunction;
  }
  /**
   * @return NetworkEndpointGroupCloudFunction
   */
  public function getCloudFunction()
  {
    return $this->cloudFunction;
  }
  /**
   * Optional. Only valid when networkEndpointType isSERVERLESS. Only one of
   * cloudRun,appEngine or cloudFunction may be set.
   *
   * @param NetworkEndpointGroupCloudRun $cloudRun
   */
  public function setCloudRun(NetworkEndpointGroupCloudRun $cloudRun)
  {
    $this->cloudRun = $cloudRun;
  }
  /**
   * @return NetworkEndpointGroupCloudRun
   */
  public function getCloudRun()
  {
    return $this->cloudRun;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * The default port used if the port number is not specified in the network
   * endpoint.
   *
   * Optional. If the network endpoint type is either GCE_VM_IP,SERVERLESS or
   * PRIVATE_SERVICE_CONNECT, this field must not be specified.
   *
   * @param int $defaultPort
   */
  public function setDefaultPort($defaultPort)
  {
    $this->defaultPort = $defaultPort;
  }
  /**
   * @return int
   */
  public function getDefaultPort()
  {
    return $this->defaultPort;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#networkEndpointGroup for network endpoint group.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * The URL of the network to which all network endpoints in the NEG belong.
   * Uses default project network if unspecified.
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
   * Type of network endpoints in this network endpoint group. Can be one
   * ofGCE_VM_IP, GCE_VM_IP_PORT,NON_GCP_PRIVATE_IP_PORT,
   * INTERNET_FQDN_PORT,INTERNET_IP_PORT, SERVERLESS,PRIVATE_SERVICE_CONNECT,
   * GCE_VM_IP_PORTMAP.
   *
   * Accepted values: GCE_VM_IP, GCE_VM_IP_PORT, GCE_VM_IP_PORTMAP,
   * INTERNET_FQDN_PORT, INTERNET_IP_PORT, NON_GCP_PRIVATE_IP_PORT,
   * PRIVATE_SERVICE_CONNECT, SERVERLESS
   *
   * @param self::NETWORK_ENDPOINT_TYPE_* $networkEndpointType
   */
  public function setNetworkEndpointType($networkEndpointType)
  {
    $this->networkEndpointType = $networkEndpointType;
  }
  /**
   * @return self::NETWORK_ENDPOINT_TYPE_*
   */
  public function getNetworkEndpointType()
  {
    return $this->networkEndpointType;
  }
  /**
   * Optional. Only valid when networkEndpointType isPRIVATE_SERVICE_CONNECT.
   *
   * @param NetworkEndpointGroupPscData $pscData
   */
  public function setPscData(NetworkEndpointGroupPscData $pscData)
  {
    $this->pscData = $pscData;
  }
  /**
   * @return NetworkEndpointGroupPscData
   */
  public function getPscData()
  {
    return $this->pscData;
  }
  /**
   * The target service url used to set up private service connection to a
   * Google API or a PSC Producer Service Attachment. An example value is: asia-
   * northeast3-cloudkms.googleapis.com.
   *
   * Optional. Only valid when networkEndpointType isPRIVATE_SERVICE_CONNECT.
   *
   * @param string $pscTargetService
   */
  public function setPscTargetService($pscTargetService)
  {
    $this->pscTargetService = $pscTargetService;
  }
  /**
   * @return string
   */
  public function getPscTargetService()
  {
    return $this->pscTargetService;
  }
  /**
   * Output only. [Output Only] The URL of theregion where the network endpoint
   * group is located.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output only] Number of network endpoints in the network
   * endpoint group.
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * Optional URL of the subnetwork to which all network endpoints in the NEG
   * belong.
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
   * Output only. [Output Only] The URL of thezone where the network endpoint
   * group is located.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroup::class, 'Google_Service_Compute_NetworkEndpointGroup');
