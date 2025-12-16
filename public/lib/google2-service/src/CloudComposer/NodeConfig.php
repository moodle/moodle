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

namespace Google\Service\CloudComposer;

class NodeConfig extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Optional. The IP range in CIDR notation to use internally by Cloud
   * Composer. IP addresses are not reserved - and the same range can be used by
   * multiple Cloud Composer environments. In case of overlap, IPs from this
   * range will not be accessible in the user's VPC network. Cannot be updated.
   * If not specified, the default value of '100.64.128.0/20' is used. This
   * field is supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * @var string
   */
  public $composerInternalIpv4CidrBlock;
  /**
   * Optional. Network Attachment that Cloud Composer environment is connected
   * to, which provides connectivity with a user's VPC network. Takes precedence
   * over network and subnetwork settings. If not provided, but network and
   * subnetwork are defined during environment, it will be provisioned. If not
   * provided and network and subnetwork are also empty, then connectivity to
   * user's VPC network is disabled. Network attachment must be provided in
   * format
   * projects/{project}/regions/{region}/networkAttachments/{networkAttachment}.
   * This field is supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * @var string
   */
  public $composerNetworkAttachment;
  /**
   * Optional. The disk size in GB used for node VMs. Minimum size is 30GB. If
   * unspecified, defaults to 100GB. Cannot be updated. This field is supported
   * for Cloud Composer environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Optional. Deploys 'ip-masq-agent' daemon set in the GKE cluster and defines
   * nonMasqueradeCIDRs equals to pod IP range so IP masquerading is used for
   * all destination addresses, except between pods traffic. See:
   * https://cloud.google.com/kubernetes-engine/docs/how-to/ip-masquerade-agent
   *
   * @var bool
   */
  public $enableIpMasqAgent;
  protected $ipAllocationPolicyType = IPAllocationPolicy::class;
  protected $ipAllocationPolicyDataType = '';
  /**
   * Optional. The Compute Engine [zone](/compute/docs/regions-zones) in which
   * to deploy the VMs used to run the Apache Airflow software, specified as a
   * [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/zones/{zoneId}". This `location` must belong to the
   * enclosing environment's project and location. If both this field and
   * `nodeConfig.machineType` are specified, `nodeConfig.machineType` must
   * belong to this `location`; if both are unspecified, the service will pick a
   * zone in the Compute Engine region corresponding to the Cloud Composer
   * location, and propagate that choice to both fields. If only one field
   * (`location` or `nodeConfig.machineType`) is specified, the location
   * information from the specified field will be propagated to the unspecified
   * field. This field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The Compute Engine [machine type](/compute/docs/machine-types)
   * used for cluster instances, specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/zones/{zoneId}/machineTypes/{machineTypeId}". The
   * `machineType` must belong to the enclosing environment's project and
   * location. If both this field and `nodeConfig.location` are specified, this
   * `machineType` must belong to the `nodeConfig.location`; if both are
   * unspecified, the service will pick a zone in the Compute Engine region
   * corresponding to the Cloud Composer location, and propagate that choice to
   * both fields. If exactly one of this field and `nodeConfig.location` is
   * specified, the location information from the specified field will be
   * propagated to the unspecified field. The `machineTypeId` must not be a
   * [shared-core machine type](/compute/docs/machine-types#sharedcore). If this
   * field is unspecified, the `machineTypeId` defaults to "n1-standard-1". This
   * field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The Compute Engine network to be used for machine communications,
   * specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/global/networks/{networkId}". If unspecified, the
   * "default" network ID in the environment's project is used. If a [Custom
   * Subnet Network](/vpc/docs/vpc#vpc_networks_and_subnets) is provided,
   * `nodeConfig.subnetwork` must also be provided. For [Shared
   * VPC](/vpc/docs/shared-vpc) subnetwork requirements, see
   * `nodeConfig.subnetwork`.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The set of Google API scopes to be made available on all node
   * VMs. If `oauth_scopes` is empty, defaults to
   * ["https://www.googleapis.com/auth/cloud-platform"]. Cannot be updated. This
   * field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @var string[]
   */
  public $oauthScopes;
  /**
   * Optional. The Google Cloud Platform Service Account to be used by the node
   * VMs. If a service account is not specified, the "default" Compute Engine
   * service account is used. Cannot be updated.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. The Compute Engine subnetwork to be used for machine
   * communications, specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/regions/{regionId}/subnetworks/{subnetworkId}" If a
   * subnetwork is provided, `nodeConfig.network` must also be provided, and the
   * subnetwork must belong to the enclosing environment's project and location.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Optional. The list of instance tags applied to all node VMs. Tags are used
   * to identify valid sources or targets for network firewalls. Each tag within
   * the list must comply with [RFC1035](https://www.ietf.org/rfc/rfc1035.txt).
   * Cannot be updated.
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. The IP range in CIDR notation to use internally by Cloud
   * Composer. IP addresses are not reserved - and the same range can be used by
   * multiple Cloud Composer environments. In case of overlap, IPs from this
   * range will not be accessible in the user's VPC network. Cannot be updated.
   * If not specified, the default value of '100.64.128.0/20' is used. This
   * field is supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * @param string $composerInternalIpv4CidrBlock
   */
  public function setComposerInternalIpv4CidrBlock($composerInternalIpv4CidrBlock)
  {
    $this->composerInternalIpv4CidrBlock = $composerInternalIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getComposerInternalIpv4CidrBlock()
  {
    return $this->composerInternalIpv4CidrBlock;
  }
  /**
   * Optional. Network Attachment that Cloud Composer environment is connected
   * to, which provides connectivity with a user's VPC network. Takes precedence
   * over network and subnetwork settings. If not provided, but network and
   * subnetwork are defined during environment, it will be provisioned. If not
   * provided and network and subnetwork are also empty, then connectivity to
   * user's VPC network is disabled. Network attachment must be provided in
   * format
   * projects/{project}/regions/{region}/networkAttachments/{networkAttachment}.
   * This field is supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * @param string $composerNetworkAttachment
   */
  public function setComposerNetworkAttachment($composerNetworkAttachment)
  {
    $this->composerNetworkAttachment = $composerNetworkAttachment;
  }
  /**
   * @return string
   */
  public function getComposerNetworkAttachment()
  {
    return $this->composerNetworkAttachment;
  }
  /**
   * Optional. The disk size in GB used for node VMs. Minimum size is 30GB. If
   * unspecified, defaults to 100GB. Cannot be updated. This field is supported
   * for Cloud Composer environments in versions composer-1.*.*-airflow-*.*.*.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Optional. Deploys 'ip-masq-agent' daemon set in the GKE cluster and defines
   * nonMasqueradeCIDRs equals to pod IP range so IP masquerading is used for
   * all destination addresses, except between pods traffic. See:
   * https://cloud.google.com/kubernetes-engine/docs/how-to/ip-masquerade-agent
   *
   * @param bool $enableIpMasqAgent
   */
  public function setEnableIpMasqAgent($enableIpMasqAgent)
  {
    $this->enableIpMasqAgent = $enableIpMasqAgent;
  }
  /**
   * @return bool
   */
  public function getEnableIpMasqAgent()
  {
    return $this->enableIpMasqAgent;
  }
  /**
   * Optional. The configuration for controlling how IPs are allocated in the
   * GKE cluster.
   *
   * @param IPAllocationPolicy $ipAllocationPolicy
   */
  public function setIpAllocationPolicy(IPAllocationPolicy $ipAllocationPolicy)
  {
    $this->ipAllocationPolicy = $ipAllocationPolicy;
  }
  /**
   * @return IPAllocationPolicy
   */
  public function getIpAllocationPolicy()
  {
    return $this->ipAllocationPolicy;
  }
  /**
   * Optional. The Compute Engine [zone](/compute/docs/regions-zones) in which
   * to deploy the VMs used to run the Apache Airflow software, specified as a
   * [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/zones/{zoneId}". This `location` must belong to the
   * enclosing environment's project and location. If both this field and
   * `nodeConfig.machineType` are specified, `nodeConfig.machineType` must
   * belong to this `location`; if both are unspecified, the service will pick a
   * zone in the Compute Engine region corresponding to the Cloud Composer
   * location, and propagate that choice to both fields. If only one field
   * (`location` or `nodeConfig.machineType`) is specified, the location
   * information from the specified field will be propagated to the unspecified
   * field. This field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. The Compute Engine [machine type](/compute/docs/machine-types)
   * used for cluster instances, specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/zones/{zoneId}/machineTypes/{machineTypeId}". The
   * `machineType` must belong to the enclosing environment's project and
   * location. If both this field and `nodeConfig.location` are specified, this
   * `machineType` must belong to the `nodeConfig.location`; if both are
   * unspecified, the service will pick a zone in the Compute Engine region
   * corresponding to the Cloud Composer location, and propagate that choice to
   * both fields. If exactly one of this field and `nodeConfig.location` is
   * specified, the location information from the specified field will be
   * propagated to the unspecified field. The `machineTypeId` must not be a
   * [shared-core machine type](/compute/docs/machine-types#sharedcore). If this
   * field is unspecified, the `machineTypeId` defaults to "n1-standard-1". This
   * field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. The Compute Engine network to be used for machine communications,
   * specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/global/networks/{networkId}". If unspecified, the
   * "default" network ID in the environment's project is used. If a [Custom
   * Subnet Network](/vpc/docs/vpc#vpc_networks_and_subnets) is provided,
   * `nodeConfig.subnetwork` must also be provided. For [Shared
   * VPC](/vpc/docs/shared-vpc) subnetwork requirements, see
   * `nodeConfig.subnetwork`.
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
   * Optional. The set of Google API scopes to be made available on all node
   * VMs. If `oauth_scopes` is empty, defaults to
   * ["https://www.googleapis.com/auth/cloud-platform"]. Cannot be updated. This
   * field is supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*.
   *
   * @param string[] $oauthScopes
   */
  public function setOauthScopes($oauthScopes)
  {
    $this->oauthScopes = $oauthScopes;
  }
  /**
   * @return string[]
   */
  public function getOauthScopes()
  {
    return $this->oauthScopes;
  }
  /**
   * Optional. The Google Cloud Platform Service Account to be used by the node
   * VMs. If a service account is not specified, the "default" Compute Engine
   * service account is used. Cannot be updated.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. The Compute Engine subnetwork to be used for machine
   * communications, specified as a [relative resource
   * name](/apis/design/resource_names#relative_resource_name). For example:
   * "projects/{projectId}/regions/{regionId}/subnetworks/{subnetworkId}" If a
   * subnetwork is provided, `nodeConfig.network` must also be provided, and the
   * subnetwork must belong to the enclosing environment's project and location.
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
   * Optional. The list of instance tags applied to all node VMs. Tags are used
   * to identify valid sources or targets for network firewalls. Each tag within
   * the list must comply with [RFC1035](https://www.ietf.org/rfc/rfc1035.txt).
   * Cannot be updated.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeConfig::class, 'Google_Service_CloudComposer_NodeConfig');
