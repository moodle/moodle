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

class NetworkAttachment extends \Google\Collection
{
  public const CONNECTION_PREFERENCE_ACCEPT_AUTOMATIC = 'ACCEPT_AUTOMATIC';
  public const CONNECTION_PREFERENCE_ACCEPT_MANUAL = 'ACCEPT_MANUAL';
  public const CONNECTION_PREFERENCE_INVALID = 'INVALID';
  protected $collection_key = 'subnetworks';
  protected $connectionEndpointsType = NetworkAttachmentConnectedEndpoint::class;
  protected $connectionEndpointsDataType = 'array';
  /**
   * @var string
   */
  public $connectionPreference;
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. An up-to-date fingerprint must be
   * provided in order to patch.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   *
   * @var string
   */
  public $kind;
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
  /**
   * Output only. [Output Only] The URL of the network which the Network
   * Attachment belongs to. Practically it is inferred by fetching the network
   * of the first subnetwork associated. Because it is required that all the
   * subnetworks must be from the same network, it is assured that the Network
   * Attachment belongs to the same network as all the subnetworks.
   *
   * @var string
   */
  public $network;
  /**
   * Projects that are allowed to connect to this network attachment. The
   * project can be specified using its id or number.
   *
   * @var string[]
   */
  public $producerAcceptLists;
  /**
   * Projects that are not allowed to connect to this network attachment. The
   * project can be specified using its id or number.
   *
   * @var string[]
   */
  public $producerRejectLists;
  /**
   * Output only. [Output Only] URL of the region where the network attachment
   * resides. This field applies only to the region resource. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
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
   * Output only. [Output Only] Server-defined URL for this resource's resource
   * id.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * An array of URLs where each entry is the URL of a subnet provided by the
   * service consumer to use for endpoints in the producers that connect to this
   * network attachment.
   *
   * @var string[]
   */
  public $subnetworks;

  /**
   * Output only. [Output Only] An array of connections for all the producers
   * connected to this network attachment.
   *
   * @param NetworkAttachmentConnectedEndpoint[] $connectionEndpoints
   */
  public function setConnectionEndpoints($connectionEndpoints)
  {
    $this->connectionEndpoints = $connectionEndpoints;
  }
  /**
   * @return NetworkAttachmentConnectedEndpoint[]
   */
  public function getConnectionEndpoints()
  {
    return $this->connectionEndpoints;
  }
  /**
   * @param self::CONNECTION_PREFERENCE_* $connectionPreference
   */
  public function setConnectionPreference($connectionPreference)
  {
    $this->connectionPreference = $connectionPreference;
  }
  /**
   * @return self::CONNECTION_PREFERENCE_*
   */
  public function getConnectionPreference()
  {
    return $this->connectionPreference;
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. An up-to-date fingerprint must be
   * provided in order to patch.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
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
   * Output only. [Output Only] The URL of the network which the Network
   * Attachment belongs to. Practically it is inferred by fetching the network
   * of the first subnetwork associated. Because it is required that all the
   * subnetworks must be from the same network, it is assured that the Network
   * Attachment belongs to the same network as all the subnetworks.
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
   * Projects that are allowed to connect to this network attachment. The
   * project can be specified using its id or number.
   *
   * @param string[] $producerAcceptLists
   */
  public function setProducerAcceptLists($producerAcceptLists)
  {
    $this->producerAcceptLists = $producerAcceptLists;
  }
  /**
   * @return string[]
   */
  public function getProducerAcceptLists()
  {
    return $this->producerAcceptLists;
  }
  /**
   * Projects that are not allowed to connect to this network attachment. The
   * project can be specified using its id or number.
   *
   * @param string[] $producerRejectLists
   */
  public function setProducerRejectLists($producerRejectLists)
  {
    $this->producerRejectLists = $producerRejectLists;
  }
  /**
   * @return string[]
   */
  public function getProducerRejectLists()
  {
    return $this->producerRejectLists;
  }
  /**
   * Output only. [Output Only] URL of the region where the network attachment
   * resides. This field applies only to the region resource. You must specify
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
   * Output only. [Output Only] Server-defined URL for this resource's resource
   * id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * An array of URLs where each entry is the URL of a subnet provided by the
   * service consumer to use for endpoints in the producers that connect to this
   * network attachment.
   *
   * @param string[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return string[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAttachment::class, 'Google_Service_Compute_NetworkAttachment');
