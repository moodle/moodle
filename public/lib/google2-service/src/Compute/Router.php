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

class Router extends \Google\Collection
{
  protected $collection_key = 'nats';
  protected $bgpType = RouterBgp::class;
  protected $bgpDataType = '';
  protected $bgpPeersType = RouterBgpPeer::class;
  protected $bgpPeersDataType = 'array';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates if a router is dedicated for use with encrypted VLAN attachments
   * (interconnectAttachments).
   *
   * @var bool
   */
  public $encryptedInterconnectRouter;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  protected $interfacesType = RouterInterface::class;
  protected $interfacesDataType = 'array';
  /**
   * Output only. [Output Only] Type of resource. Always compute#router for
   * routers.
   *
   * @var string
   */
  public $kind;
  protected $md5AuthenticationKeysType = RouterMd5AuthenticationKey::class;
  protected $md5AuthenticationKeysDataType = 'array';
  /**
   * Name of the resource. Provided by the client when the resource is created.
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
  protected $natsType = RouterNat::class;
  protected $natsDataType = 'array';
  /**
   * URI of the network to which this router belongs.
   *
   * @var string
   */
  public $network;
  protected $paramsType = RouterParams::class;
  protected $paramsDataType = '';
  /**
   * [Output Only] URI of the region where the router resides. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;

  /**
   * BGP information specific to this router.
   *
   * @param RouterBgp $bgp
   */
  public function setBgp(RouterBgp $bgp)
  {
    $this->bgp = $bgp;
  }
  /**
   * @return RouterBgp
   */
  public function getBgp()
  {
    return $this->bgp;
  }
  /**
   * BGP information that must be configured into the routing stack to establish
   * BGP peering. This information must specify the peer ASN and either the
   * interface name, IP address, or peer IP address. Please refer toRFC4273.
   *
   * @param RouterBgpPeer[] $bgpPeers
   */
  public function setBgpPeers($bgpPeers)
  {
    $this->bgpPeers = $bgpPeers;
  }
  /**
   * @return RouterBgpPeer[]
   */
  public function getBgpPeers()
  {
    return $this->bgpPeers;
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
   * Indicates if a router is dedicated for use with encrypted VLAN attachments
   * (interconnectAttachments).
   *
   * @param bool $encryptedInterconnectRouter
   */
  public function setEncryptedInterconnectRouter($encryptedInterconnectRouter)
  {
    $this->encryptedInterconnectRouter = $encryptedInterconnectRouter;
  }
  /**
   * @return bool
   */
  public function getEncryptedInterconnectRouter()
  {
    return $this->encryptedInterconnectRouter;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * Router interfaces. To create a BGP peer that uses a router interface, the
   * interface must have one of the following fields specified:        -
   * linkedVpnTunnel    - linkedInterconnectAttachment    - subnetwork
   *
   * You can create a router interface without any of these fields specified.
   * However, you cannot create a BGP peer that uses that interface.
   *
   * @param RouterInterface[] $interfaces
   */
  public function setInterfaces($interfaces)
  {
    $this->interfaces = $interfaces;
  }
  /**
   * @return RouterInterface[]
   */
  public function getInterfaces()
  {
    return $this->interfaces;
  }
  /**
   * Output only. [Output Only] Type of resource. Always compute#router for
   * routers.
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
   * Keys used for MD5 authentication.
   *
   * @param RouterMd5AuthenticationKey[] $md5AuthenticationKeys
   */
  public function setMd5AuthenticationKeys($md5AuthenticationKeys)
  {
    $this->md5AuthenticationKeys = $md5AuthenticationKeys;
  }
  /**
   * @return RouterMd5AuthenticationKey[]
   */
  public function getMd5AuthenticationKeys()
  {
    return $this->md5AuthenticationKeys;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
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
   * A list of NAT services created in this router.
   *
   * @param RouterNat[] $nats
   */
  public function setNats($nats)
  {
    $this->nats = $nats;
  }
  /**
   * @return RouterNat[]
   */
  public function getNats()
  {
    return $this->nats;
  }
  /**
   * URI of the network to which this router belongs.
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
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param RouterParams $params
   */
  public function setParams(RouterParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return RouterParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * [Output Only] URI of the region where the router resides. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
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
   * [Output Only] Server-defined URL for the resource.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Router::class, 'Google_Service_Compute_Router');
