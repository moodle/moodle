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

namespace Google\Service\ServiceNetworking;

class AddSubnetworkRequest extends \Google\Collection
{
  protected $collection_key = 'subnetworkUsers';
  /**
   * Optional. Defines the allowSubnetCidrRoutesOverlap field of the subnet,
   * e.g. Available in alpha and beta according to [Compute API documentation](h
   * ttps://cloud.google.com/compute/docs/reference/rest/beta/subnetworks/insert
   * )
   *
   * @var bool
   */
  public $allowSubnetCidrRoutesOverlap;
  /**
   * Optional. The IAM permission check determines whether the consumer project
   * has 'servicenetworking.services.use' permission or not.
   *
   * @var bool
   */
  public $checkServiceNetworkingUsePermission;
  /**
   * Optional. Specifies a custom time bucket for GCE subnetwork request
   * idempotency. If two equivalent concurrent requests are made, GCE will know
   * to ignore the request if it has already been completed or is in progress.
   * Only requests with matching compute_idempotency_window have guaranteed
   * idempotency. Changing this time window between requests results in
   * undefined behavior. Zero (or empty) value with
   * custom_compute_idempotency_window=true specifies no idempotency (i.e. no
   * request ID is provided to GCE). Maximum value of 14 days (enforced by GCE
   * limit).
   *
   * @var string
   */
  public $computeIdempotencyWindow;
  /**
   * Required. A resource that represents the service consumer, such as
   * `projects/123456`. The project number can be different from the value in
   * the consumer network parameter. For example, the network might be part of a
   * Shared VPC network. In those cases, Service Networking validates that this
   * resource belongs to that Shared VPC.
   *
   * @var string
   */
  public $consumer;
  /**
   * Required. The name of the service consumer's VPC network. The network must
   * have an existing private connection that was provisioned through the
   * connections.create method. The name must be in the following format:
   * `projects/{project}/global/networks/{network}`, where {project} is a
   * project number, such as `12345`. {network} is the name of a VPC network in
   * the project.
   *
   * @var string
   */
  public $consumerNetwork;
  /**
   * Optional. Description of the subnet.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The url of an Internal Range. Eg:
   * `projects//locations/global/internalRanges/`. If specified, it means that
   * the subnetwork cidr will be created using the combination of
   * requested_address/ip_prefix_length. Note that the subnet cidr has to be
   * within the cidr range of this Internal Range.
   *
   * @var string
   */
  public $internalRange;
  /**
   * Required. The prefix length of the subnet's IP address range. Use CIDR
   * range notation, such as `29` to provision a subnet with an `x.x.x.x/29`
   * CIDR range. The IP address range is drawn from a pool of available ranges
   * in the service consumer's allocated range. GCE disallows subnets with
   * prefix_length > 29
   *
   * @var int
   */
  public $ipPrefixLength;
  /**
   * Optional. Enable outside allocation using public IP addresses. Any public
   * IP range may be specified. If this field is provided, we will not use
   * customer reserved ranges for this primary IP range.
   *
   * @var string
   */
  public $outsideAllocationPublicIpRange;
  /**
   * Optional. The private IPv6 google access type for the VMs in this subnet.
   * For information about the access types that can be set using this field,
   * see [subnetwork](https://cloud.google.com/compute/docs/reference/rest/v1/su
   * bnetworks) in the Compute API documentation.
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  /**
   * Optional. Defines the purpose field of the subnet, e.g.
   * 'PRIVATE_SERVICE_CONNECT'. For information about the purposes that can be
   * set using this field, see [subnetwork](https://cloud.google.com/compute/doc
   * s/reference/rest/v1/subnetworks) in the Compute API documentation.
   *
   * @var string
   */
  public $purpose;
  /**
   * Required. The name of a
   * [region](https://cloud.google.com/compute/docs/regions-zones) for the
   * subnet, such `europe-west1`.
   *
   * @var string
   */
  public $region;
  /**
   * Optional. The starting address of a range. The address must be a valid IPv4
   * address in the x.x.x.x format. This value combined with the IP prefix range
   * is the CIDR range for the subnet. The range must be within the allocated
   * range that is assigned to the private connection. If the CIDR range isn't
   * available, the call fails.
   *
   * @var string
   */
  public $requestedAddress;
  /**
   * Optional. The name of one or more allocated IP address ranges associated
   * with this private service access connection. If no range names are provided
   * all ranges associated with this connection will be considered. If a CIDR
   * range with the specified IP prefix length is not available within these
   * ranges, the call fails.
   *
   * @var string[]
   */
  public $requestedRanges;
  /**
   * Optional. Defines the role field of the subnet, e.g. 'ACTIVE'. For
   * information about the roles that can be set using this field, see [subnetwo
   * rk](https://cloud.google.com/compute/docs/reference/rest/v1/subnetworks) in
   * the Compute API documentation.
   *
   * @var string
   */
  public $role;
  protected $secondaryIpRangeSpecsType = SecondaryIpRangeSpec::class;
  protected $secondaryIpRangeSpecsDataType = 'array';
  /**
   * Optional. Skips validating if the requested_address is in use by SN VPC’s
   * peering group. Compute Engine will still perform this check and fail the
   * request if the requested_address is in use. Note that Compute Engine does
   * not check for the existence of dynamic routes when performing this check.
   * Caller of this API should make sure that there are no dynamic routes
   * overlapping with the requested_address/prefix_length IP address range
   * otherwise the created subnet could cause misrouting.
   *
   * @var bool
   */
  public $skipRequestedAddressValidation;
  /**
   * Required. A name for the new subnet. For information about the naming
   * requirements, see [subnetwork](https://cloud.google.com/compute/docs/refere
   * nce/rest/v1/subnetworks) in the Compute API documentation.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * A list of members that are granted the
   * `roles/servicenetworking.subnetworkAdmin` role on the subnet.
   *
   * @var string[]
   */
  public $subnetworkUsers;
  /**
   * Optional. Specifies if Service Networking should use a custom time bucket
   * for GCE idempotency. If false, Service Networking uses a 300 second (5
   * minute) GCE idempotency window. If true, Service Networking uses a custom
   * idempotency window provided by the user in field
   * compute_idempotency_window.
   *
   * @var bool
   */
  public $useCustomComputeIdempotencyWindow;

  /**
   * Optional. Defines the allowSubnetCidrRoutesOverlap field of the subnet,
   * e.g. Available in alpha and beta according to [Compute API documentation](h
   * ttps://cloud.google.com/compute/docs/reference/rest/beta/subnetworks/insert
   * )
   *
   * @param bool $allowSubnetCidrRoutesOverlap
   */
  public function setAllowSubnetCidrRoutesOverlap($allowSubnetCidrRoutesOverlap)
  {
    $this->allowSubnetCidrRoutesOverlap = $allowSubnetCidrRoutesOverlap;
  }
  /**
   * @return bool
   */
  public function getAllowSubnetCidrRoutesOverlap()
  {
    return $this->allowSubnetCidrRoutesOverlap;
  }
  /**
   * Optional. The IAM permission check determines whether the consumer project
   * has 'servicenetworking.services.use' permission or not.
   *
   * @param bool $checkServiceNetworkingUsePermission
   */
  public function setCheckServiceNetworkingUsePermission($checkServiceNetworkingUsePermission)
  {
    $this->checkServiceNetworkingUsePermission = $checkServiceNetworkingUsePermission;
  }
  /**
   * @return bool
   */
  public function getCheckServiceNetworkingUsePermission()
  {
    return $this->checkServiceNetworkingUsePermission;
  }
  /**
   * Optional. Specifies a custom time bucket for GCE subnetwork request
   * idempotency. If two equivalent concurrent requests are made, GCE will know
   * to ignore the request if it has already been completed or is in progress.
   * Only requests with matching compute_idempotency_window have guaranteed
   * idempotency. Changing this time window between requests results in
   * undefined behavior. Zero (or empty) value with
   * custom_compute_idempotency_window=true specifies no idempotency (i.e. no
   * request ID is provided to GCE). Maximum value of 14 days (enforced by GCE
   * limit).
   *
   * @param string $computeIdempotencyWindow
   */
  public function setComputeIdempotencyWindow($computeIdempotencyWindow)
  {
    $this->computeIdempotencyWindow = $computeIdempotencyWindow;
  }
  /**
   * @return string
   */
  public function getComputeIdempotencyWindow()
  {
    return $this->computeIdempotencyWindow;
  }
  /**
   * Required. A resource that represents the service consumer, such as
   * `projects/123456`. The project number can be different from the value in
   * the consumer network parameter. For example, the network might be part of a
   * Shared VPC network. In those cases, Service Networking validates that this
   * resource belongs to that Shared VPC.
   *
   * @param string $consumer
   */
  public function setConsumer($consumer)
  {
    $this->consumer = $consumer;
  }
  /**
   * @return string
   */
  public function getConsumer()
  {
    return $this->consumer;
  }
  /**
   * Required. The name of the service consumer's VPC network. The network must
   * have an existing private connection that was provisioned through the
   * connections.create method. The name must be in the following format:
   * `projects/{project}/global/networks/{network}`, where {project} is a
   * project number, such as `12345`. {network} is the name of a VPC network in
   * the project.
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
   * Optional. Description of the subnet.
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
   * Optional. The url of an Internal Range. Eg:
   * `projects//locations/global/internalRanges/`. If specified, it means that
   * the subnetwork cidr will be created using the combination of
   * requested_address/ip_prefix_length. Note that the subnet cidr has to be
   * within the cidr range of this Internal Range.
   *
   * @param string $internalRange
   */
  public function setInternalRange($internalRange)
  {
    $this->internalRange = $internalRange;
  }
  /**
   * @return string
   */
  public function getInternalRange()
  {
    return $this->internalRange;
  }
  /**
   * Required. The prefix length of the subnet's IP address range. Use CIDR
   * range notation, such as `29` to provision a subnet with an `x.x.x.x/29`
   * CIDR range. The IP address range is drawn from a pool of available ranges
   * in the service consumer's allocated range. GCE disallows subnets with
   * prefix_length > 29
   *
   * @param int $ipPrefixLength
   */
  public function setIpPrefixLength($ipPrefixLength)
  {
    $this->ipPrefixLength = $ipPrefixLength;
  }
  /**
   * @return int
   */
  public function getIpPrefixLength()
  {
    return $this->ipPrefixLength;
  }
  /**
   * Optional. Enable outside allocation using public IP addresses. Any public
   * IP range may be specified. If this field is provided, we will not use
   * customer reserved ranges for this primary IP range.
   *
   * @param string $outsideAllocationPublicIpRange
   */
  public function setOutsideAllocationPublicIpRange($outsideAllocationPublicIpRange)
  {
    $this->outsideAllocationPublicIpRange = $outsideAllocationPublicIpRange;
  }
  /**
   * @return string
   */
  public function getOutsideAllocationPublicIpRange()
  {
    return $this->outsideAllocationPublicIpRange;
  }
  /**
   * Optional. The private IPv6 google access type for the VMs in this subnet.
   * For information about the access types that can be set using this field,
   * see [subnetwork](https://cloud.google.com/compute/docs/reference/rest/v1/su
   * bnetworks) in the Compute API documentation.
   *
   * @param string $privateIpv6GoogleAccess
   */
  public function setPrivateIpv6GoogleAccess($privateIpv6GoogleAccess)
  {
    $this->privateIpv6GoogleAccess = $privateIpv6GoogleAccess;
  }
  /**
   * @return string
   */
  public function getPrivateIpv6GoogleAccess()
  {
    return $this->privateIpv6GoogleAccess;
  }
  /**
   * Optional. Defines the purpose field of the subnet, e.g.
   * 'PRIVATE_SERVICE_CONNECT'. For information about the purposes that can be
   * set using this field, see [subnetwork](https://cloud.google.com/compute/doc
   * s/reference/rest/v1/subnetworks) in the Compute API documentation.
   *
   * @param string $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return string
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * Required. The name of a
   * [region](https://cloud.google.com/compute/docs/regions-zones) for the
   * subnet, such `europe-west1`.
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
   * Optional. The starting address of a range. The address must be a valid IPv4
   * address in the x.x.x.x format. This value combined with the IP prefix range
   * is the CIDR range for the subnet. The range must be within the allocated
   * range that is assigned to the private connection. If the CIDR range isn't
   * available, the call fails.
   *
   * @param string $requestedAddress
   */
  public function setRequestedAddress($requestedAddress)
  {
    $this->requestedAddress = $requestedAddress;
  }
  /**
   * @return string
   */
  public function getRequestedAddress()
  {
    return $this->requestedAddress;
  }
  /**
   * Optional. The name of one or more allocated IP address ranges associated
   * with this private service access connection. If no range names are provided
   * all ranges associated with this connection will be considered. If a CIDR
   * range with the specified IP prefix length is not available within these
   * ranges, the call fails.
   *
   * @param string[] $requestedRanges
   */
  public function setRequestedRanges($requestedRanges)
  {
    $this->requestedRanges = $requestedRanges;
  }
  /**
   * @return string[]
   */
  public function getRequestedRanges()
  {
    return $this->requestedRanges;
  }
  /**
   * Optional. Defines the role field of the subnet, e.g. 'ACTIVE'. For
   * information about the roles that can be set using this field, see [subnetwo
   * rk](https://cloud.google.com/compute/docs/reference/rest/v1/subnetworks) in
   * the Compute API documentation.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Optional. A list of secondary IP ranges to be created within the new
   * subnetwork.
   *
   * @param SecondaryIpRangeSpec[] $secondaryIpRangeSpecs
   */
  public function setSecondaryIpRangeSpecs($secondaryIpRangeSpecs)
  {
    $this->secondaryIpRangeSpecs = $secondaryIpRangeSpecs;
  }
  /**
   * @return SecondaryIpRangeSpec[]
   */
  public function getSecondaryIpRangeSpecs()
  {
    return $this->secondaryIpRangeSpecs;
  }
  /**
   * Optional. Skips validating if the requested_address is in use by SN VPC’s
   * peering group. Compute Engine will still perform this check and fail the
   * request if the requested_address is in use. Note that Compute Engine does
   * not check for the existence of dynamic routes when performing this check.
   * Caller of this API should make sure that there are no dynamic routes
   * overlapping with the requested_address/prefix_length IP address range
   * otherwise the created subnet could cause misrouting.
   *
   * @param bool $skipRequestedAddressValidation
   */
  public function setSkipRequestedAddressValidation($skipRequestedAddressValidation)
  {
    $this->skipRequestedAddressValidation = $skipRequestedAddressValidation;
  }
  /**
   * @return bool
   */
  public function getSkipRequestedAddressValidation()
  {
    return $this->skipRequestedAddressValidation;
  }
  /**
   * Required. A name for the new subnet. For information about the naming
   * requirements, see [subnetwork](https://cloud.google.com/compute/docs/refere
   * nce/rest/v1/subnetworks) in the Compute API documentation.
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
   * A list of members that are granted the
   * `roles/servicenetworking.subnetworkAdmin` role on the subnet.
   *
   * @param string[] $subnetworkUsers
   */
  public function setSubnetworkUsers($subnetworkUsers)
  {
    $this->subnetworkUsers = $subnetworkUsers;
  }
  /**
   * @return string[]
   */
  public function getSubnetworkUsers()
  {
    return $this->subnetworkUsers;
  }
  /**
   * Optional. Specifies if Service Networking should use a custom time bucket
   * for GCE idempotency. If false, Service Networking uses a 300 second (5
   * minute) GCE idempotency window. If true, Service Networking uses a custom
   * idempotency window provided by the user in field
   * compute_idempotency_window.
   *
   * @param bool $useCustomComputeIdempotencyWindow
   */
  public function setUseCustomComputeIdempotencyWindow($useCustomComputeIdempotencyWindow)
  {
    $this->useCustomComputeIdempotencyWindow = $useCustomComputeIdempotencyWindow;
  }
  /**
   * @return bool
   */
  public function getUseCustomComputeIdempotencyWindow()
  {
    return $this->useCustomComputeIdempotencyWindow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddSubnetworkRequest::class, 'Google_Service_ServiceNetworking_AddSubnetworkRequest');
