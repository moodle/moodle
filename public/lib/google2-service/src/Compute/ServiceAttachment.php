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

class ServiceAttachment extends \Google\Collection
{
  public const CONNECTION_PREFERENCE_ACCEPT_AUTOMATIC = 'ACCEPT_AUTOMATIC';
  public const CONNECTION_PREFERENCE_ACCEPT_MANUAL = 'ACCEPT_MANUAL';
  public const CONNECTION_PREFERENCE_CONNECTION_PREFERENCE_UNSPECIFIED = 'CONNECTION_PREFERENCE_UNSPECIFIED';
  protected $collection_key = 'natSubnets';
  protected $connectedEndpointsType = ServiceAttachmentConnectedEndpoint::class;
  protected $connectedEndpointsDataType = 'array';
  /**
   * The connection preference of service attachment. The value can be set to
   * ACCEPT_AUTOMATIC. An ACCEPT_AUTOMATIC service attachment is one that always
   * accepts the connection from consumer forwarding rules.
   *
   * @var string
   */
  public $connectionPreference;
  protected $consumerAcceptListsType = ServiceAttachmentConsumerProjectLimit::class;
  protected $consumerAcceptListsDataType = 'array';
  /**
   * Specifies a list of projects or networks that are not allowed to connect to
   * this service attachment. The project can be specified using its project ID
   * or project number and the network can be specified using its URL. A given
   * service attachment can manage connections at either the project or network
   * level. Therefore, both the reject and accept lists for a given service
   * attachment must contain either only projects or only networks.
   *
   * @var string[]
   */
  public $consumerRejectLists;
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
   * If specified, the domain name will be used during the integration between
   * the PSC connected endpoints and the Cloud DNS. For example, this is a valid
   * domain name: "p.mycompany.com.". Current max number of domain names
   * supported is 1.
   *
   * @var string[]
   */
  public $domainNames;
  /**
   * If true, enable the proxy protocol which is for supplying client TCP/IP
   * address data in TCP connections that traverse proxies on their way to
   * destination servers.
   *
   * @var bool
   */
  public $enableProxyProtocol;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a ServiceAttachment. An up-to-date fingerprint must be provided
   * in order to patch/update the ServiceAttachment; otherwise, the request will
   * fail with error 412 conditionNotMet. To see the latest fingerprint, make a
   * get() request to retrieve the ServiceAttachment.
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
   * Alwayscompute#serviceAttachment for service attachments.
   *
   * @var string
   */
  public $kind;
  /**
   * Metadata of the service attachment.
   *
   * @var string[]
   */
  public $metadata;
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
   * An array of URLs where each entry is the URL of a subnet provided by the
   * service producer to use for NAT in this service attachment.
   *
   * @var string[]
   */
  public $natSubnets;
  /**
   * The URL of a forwarding rule with loadBalancingScheme INTERNAL* that is
   * serving the endpoint identified by this service attachment.
   *
   * @deprecated
   * @var string
   */
  public $producerForwardingRule;
  /**
   * The number of consumer spokes that connected Private Service Connect
   * endpoints can be propagated to through Network Connectivity Center. This
   * limit lets the service producer limit how many propagated Private Service
   * Connect connections can be established to this service attachment from a
   * single consumer.
   *
   * If the connection preference of the service attachment is ACCEPT_MANUAL,
   * the limit applies to each project or network that is listed in the consumer
   * accept list. If the connection preference of the service attachment is
   * ACCEPT_AUTOMATIC, the limit applies to each project that contains a
   * connected endpoint.
   *
   * If unspecified, the default propagated connection limit is 250.
   *
   * @var string
   */
  public $propagatedConnectionLimit;
  protected $pscServiceAttachmentIdType = Uint128::class;
  protected $pscServiceAttachmentIdDataType = '';
  /**
   * This flag determines whether a consumer accept/reject list change can
   * reconcile the statuses of existing ACCEPTED or REJECTED PSC endpoints.
   * -  If false, connection policy update will only affect existing PENDING
   * PSC endpoints. Existing ACCEPTED/REJECTED endpoints will remain untouched
   * regardless how the connection policy is modified .     -  If true,
   * update will affect both PENDING and ACCEPTED/REJECTED PSC endpoints. For
   * example, an ACCEPTED PSC endpoint will be moved to REJECTED if its project
   * is added to the reject list.
   *
   * For newly created service attachment, this boolean defaults to false.
   *
   * @var bool
   */
  public $reconcileConnections;
  /**
   * Output only. [Output Only] URL of the region where the service attachment
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
   * The URL of a service serving the endpoint identified by this service
   * attachment.
   *
   * @var string
   */
  public $targetService;

  /**
   * Output only. [Output Only] An array of connections for all the consumers
   * connected to this service attachment.
   *
   * @param ServiceAttachmentConnectedEndpoint[] $connectedEndpoints
   */
  public function setConnectedEndpoints($connectedEndpoints)
  {
    $this->connectedEndpoints = $connectedEndpoints;
  }
  /**
   * @return ServiceAttachmentConnectedEndpoint[]
   */
  public function getConnectedEndpoints()
  {
    return $this->connectedEndpoints;
  }
  /**
   * The connection preference of service attachment. The value can be set to
   * ACCEPT_AUTOMATIC. An ACCEPT_AUTOMATIC service attachment is one that always
   * accepts the connection from consumer forwarding rules.
   *
   * Accepted values: ACCEPT_AUTOMATIC, ACCEPT_MANUAL,
   * CONNECTION_PREFERENCE_UNSPECIFIED
   *
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
   * Specifies which consumer projects or networks are allowed to connect to the
   * service attachment. Each project or network has a connection limit. A given
   * service attachment can manage connections at either the project or network
   * level. Therefore, both the accept and reject lists for a given service
   * attachment must contain either only projects or only networks or only
   * endpoints.
   *
   * @param ServiceAttachmentConsumerProjectLimit[] $consumerAcceptLists
   */
  public function setConsumerAcceptLists($consumerAcceptLists)
  {
    $this->consumerAcceptLists = $consumerAcceptLists;
  }
  /**
   * @return ServiceAttachmentConsumerProjectLimit[]
   */
  public function getConsumerAcceptLists()
  {
    return $this->consumerAcceptLists;
  }
  /**
   * Specifies a list of projects or networks that are not allowed to connect to
   * this service attachment. The project can be specified using its project ID
   * or project number and the network can be specified using its URL. A given
   * service attachment can manage connections at either the project or network
   * level. Therefore, both the reject and accept lists for a given service
   * attachment must contain either only projects or only networks.
   *
   * @param string[] $consumerRejectLists
   */
  public function setConsumerRejectLists($consumerRejectLists)
  {
    $this->consumerRejectLists = $consumerRejectLists;
  }
  /**
   * @return string[]
   */
  public function getConsumerRejectLists()
  {
    return $this->consumerRejectLists;
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
   * If specified, the domain name will be used during the integration between
   * the PSC connected endpoints and the Cloud DNS. For example, this is a valid
   * domain name: "p.mycompany.com.". Current max number of domain names
   * supported is 1.
   *
   * @param string[] $domainNames
   */
  public function setDomainNames($domainNames)
  {
    $this->domainNames = $domainNames;
  }
  /**
   * @return string[]
   */
  public function getDomainNames()
  {
    return $this->domainNames;
  }
  /**
   * If true, enable the proxy protocol which is for supplying client TCP/IP
   * address data in TCP connections that traverse proxies on their way to
   * destination servers.
   *
   * @param bool $enableProxyProtocol
   */
  public function setEnableProxyProtocol($enableProxyProtocol)
  {
    $this->enableProxyProtocol = $enableProxyProtocol;
  }
  /**
   * @return bool
   */
  public function getEnableProxyProtocol()
  {
    return $this->enableProxyProtocol;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a ServiceAttachment. An up-to-date fingerprint must be provided
   * in order to patch/update the ServiceAttachment; otherwise, the request will
   * fail with error 412 conditionNotMet. To see the latest fingerprint, make a
   * get() request to retrieve the ServiceAttachment.
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
   * Alwayscompute#serviceAttachment for service attachments.
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
   * Metadata of the service attachment.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
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
   * An array of URLs where each entry is the URL of a subnet provided by the
   * service producer to use for NAT in this service attachment.
   *
   * @param string[] $natSubnets
   */
  public function setNatSubnets($natSubnets)
  {
    $this->natSubnets = $natSubnets;
  }
  /**
   * @return string[]
   */
  public function getNatSubnets()
  {
    return $this->natSubnets;
  }
  /**
   * The URL of a forwarding rule with loadBalancingScheme INTERNAL* that is
   * serving the endpoint identified by this service attachment.
   *
   * @deprecated
   * @param string $producerForwardingRule
   */
  public function setProducerForwardingRule($producerForwardingRule)
  {
    $this->producerForwardingRule = $producerForwardingRule;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProducerForwardingRule()
  {
    return $this->producerForwardingRule;
  }
  /**
   * The number of consumer spokes that connected Private Service Connect
   * endpoints can be propagated to through Network Connectivity Center. This
   * limit lets the service producer limit how many propagated Private Service
   * Connect connections can be established to this service attachment from a
   * single consumer.
   *
   * If the connection preference of the service attachment is ACCEPT_MANUAL,
   * the limit applies to each project or network that is listed in the consumer
   * accept list. If the connection preference of the service attachment is
   * ACCEPT_AUTOMATIC, the limit applies to each project that contains a
   * connected endpoint.
   *
   * If unspecified, the default propagated connection limit is 250.
   *
   * @param string $propagatedConnectionLimit
   */
  public function setPropagatedConnectionLimit($propagatedConnectionLimit)
  {
    $this->propagatedConnectionLimit = $propagatedConnectionLimit;
  }
  /**
   * @return string
   */
  public function getPropagatedConnectionLimit()
  {
    return $this->propagatedConnectionLimit;
  }
  /**
   * Output only. [Output Only] An 128-bit global unique ID of the PSC service
   * attachment.
   *
   * @param Uint128 $pscServiceAttachmentId
   */
  public function setPscServiceAttachmentId(Uint128 $pscServiceAttachmentId)
  {
    $this->pscServiceAttachmentId = $pscServiceAttachmentId;
  }
  /**
   * @return Uint128
   */
  public function getPscServiceAttachmentId()
  {
    return $this->pscServiceAttachmentId;
  }
  /**
   * This flag determines whether a consumer accept/reject list change can
   * reconcile the statuses of existing ACCEPTED or REJECTED PSC endpoints.
   * -  If false, connection policy update will only affect existing PENDING
   * PSC endpoints. Existing ACCEPTED/REJECTED endpoints will remain untouched
   * regardless how the connection policy is modified .     -  If true,
   * update will affect both PENDING and ACCEPTED/REJECTED PSC endpoints. For
   * example, an ACCEPTED PSC endpoint will be moved to REJECTED if its project
   * is added to the reject list.
   *
   * For newly created service attachment, this boolean defaults to false.
   *
   * @param bool $reconcileConnections
   */
  public function setReconcileConnections($reconcileConnections)
  {
    $this->reconcileConnections = $reconcileConnections;
  }
  /**
   * @return bool
   */
  public function getReconcileConnections()
  {
    return $this->reconcileConnections;
  }
  /**
   * Output only. [Output Only] URL of the region where the service attachment
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
   * The URL of a service serving the endpoint identified by this service
   * attachment.
   *
   * @param string $targetService
   */
  public function setTargetService($targetService)
  {
    $this->targetService = $targetService;
  }
  /**
   * @return string
   */
  public function getTargetService()
  {
    return $this->targetService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAttachment::class, 'Google_Service_Compute_ServiceAttachment');
