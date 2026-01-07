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

namespace Google\Service\Dns;

class Quota extends \Google\Collection
{
  protected $collection_key = 'whitelistedKeySpecs';
  /**
   * Maximum allowed number of DnsKeys per ManagedZone.
   *
   * @var int
   */
  public $dnsKeysPerManagedZone;
  /**
   * Maximum allowed number of GKE clusters to which a privately scoped zone can
   * be attached.
   *
   * @var int
   */
  public $gkeClustersPerManagedZone;
  /**
   * Maximum allowed number of GKE clusters per policy.
   *
   * @var int
   */
  public $gkeClustersPerPolicy;
  /**
   * Maximum allowed number of GKE clusters per response policy.
   *
   * @var int
   */
  public $gkeClustersPerResponsePolicy;
  /**
   * @var int
   */
  public $internetHealthChecksPerManagedZone;
  /**
   * Maximum allowed number of items per routing policy.
   *
   * @var int
   */
  public $itemsPerRoutingPolicy;
  /**
   * @var string
   */
  public $kind;
  /**
   * Maximum allowed number of managed zones in the project.
   *
   * @var int
   */
  public $managedZones;
  /**
   * Maximum allowed number of managed zones which can be attached to a GKE
   * cluster.
   *
   * @var int
   */
  public $managedZonesPerGkeCluster;
  /**
   * Maximum allowed number of managed zones which can be attached to a network.
   *
   * @var int
   */
  public $managedZonesPerNetwork;
  /**
   * Maximum number of nameservers per delegation, meant to prevent abuse
   *
   * @var int
   */
  public $nameserversPerDelegation;
  /**
   * Maximum allowed number of networks to which a privately scoped zone can be
   * attached.
   *
   * @var int
   */
  public $networksPerManagedZone;
  /**
   * Maximum allowed number of networks per policy.
   *
   * @var int
   */
  public $networksPerPolicy;
  /**
   * Maximum allowed number of networks per response policy.
   *
   * @var int
   */
  public $networksPerResponsePolicy;
  /**
   * Maximum allowed number of consumer peering zones per target network owned
   * by this producer project
   *
   * @var int
   */
  public $peeringZonesPerTargetNetwork;
  /**
   * Maximum allowed number of policies per project.
   *
   * @var int
   */
  public $policies;
  /**
   * Maximum allowed number of ResourceRecords per ResourceRecordSet.
   *
   * @var int
   */
  public $resourceRecordsPerRrset;
  /**
   * Maximum allowed number of response policies per project.
   *
   * @var int
   */
  public $responsePolicies;
  /**
   * Maximum allowed number of rules per response policy.
   *
   * @var int
   */
  public $responsePolicyRulesPerResponsePolicy;
  /**
   * Maximum allowed number of ResourceRecordSets to add per
   * ChangesCreateRequest.
   *
   * @var int
   */
  public $rrsetAdditionsPerChange;
  /**
   * Maximum allowed number of ResourceRecordSets to delete per
   * ChangesCreateRequest.
   *
   * @var int
   */
  public $rrsetDeletionsPerChange;
  /**
   * Maximum allowed number of ResourceRecordSets per zone in the project.
   *
   * @var int
   */
  public $rrsetsPerManagedZone;
  /**
   * Maximum allowed number of target name servers per managed forwarding zone.
   *
   * @var int
   */
  public $targetNameServersPerManagedZone;
  /**
   * Maximum allowed number of alternative target name servers per policy.
   *
   * @var int
   */
  public $targetNameServersPerPolicy;
  /**
   * Maximum allowed size for total rrdata in one ChangesCreateRequest in bytes.
   *
   * @var int
   */
  public $totalRrdataSizePerChange;
  protected $whitelistedKeySpecsType = DnsKeySpec::class;
  protected $whitelistedKeySpecsDataType = 'array';

  /**
   * Maximum allowed number of DnsKeys per ManagedZone.
   *
   * @param int $dnsKeysPerManagedZone
   */
  public function setDnsKeysPerManagedZone($dnsKeysPerManagedZone)
  {
    $this->dnsKeysPerManagedZone = $dnsKeysPerManagedZone;
  }
  /**
   * @return int
   */
  public function getDnsKeysPerManagedZone()
  {
    return $this->dnsKeysPerManagedZone;
  }
  /**
   * Maximum allowed number of GKE clusters to which a privately scoped zone can
   * be attached.
   *
   * @param int $gkeClustersPerManagedZone
   */
  public function setGkeClustersPerManagedZone($gkeClustersPerManagedZone)
  {
    $this->gkeClustersPerManagedZone = $gkeClustersPerManagedZone;
  }
  /**
   * @return int
   */
  public function getGkeClustersPerManagedZone()
  {
    return $this->gkeClustersPerManagedZone;
  }
  /**
   * Maximum allowed number of GKE clusters per policy.
   *
   * @param int $gkeClustersPerPolicy
   */
  public function setGkeClustersPerPolicy($gkeClustersPerPolicy)
  {
    $this->gkeClustersPerPolicy = $gkeClustersPerPolicy;
  }
  /**
   * @return int
   */
  public function getGkeClustersPerPolicy()
  {
    return $this->gkeClustersPerPolicy;
  }
  /**
   * Maximum allowed number of GKE clusters per response policy.
   *
   * @param int $gkeClustersPerResponsePolicy
   */
  public function setGkeClustersPerResponsePolicy($gkeClustersPerResponsePolicy)
  {
    $this->gkeClustersPerResponsePolicy = $gkeClustersPerResponsePolicy;
  }
  /**
   * @return int
   */
  public function getGkeClustersPerResponsePolicy()
  {
    return $this->gkeClustersPerResponsePolicy;
  }
  /**
   * @param int $internetHealthChecksPerManagedZone
   */
  public function setInternetHealthChecksPerManagedZone($internetHealthChecksPerManagedZone)
  {
    $this->internetHealthChecksPerManagedZone = $internetHealthChecksPerManagedZone;
  }
  /**
   * @return int
   */
  public function getInternetHealthChecksPerManagedZone()
  {
    return $this->internetHealthChecksPerManagedZone;
  }
  /**
   * Maximum allowed number of items per routing policy.
   *
   * @param int $itemsPerRoutingPolicy
   */
  public function setItemsPerRoutingPolicy($itemsPerRoutingPolicy)
  {
    $this->itemsPerRoutingPolicy = $itemsPerRoutingPolicy;
  }
  /**
   * @return int
   */
  public function getItemsPerRoutingPolicy()
  {
    return $this->itemsPerRoutingPolicy;
  }
  /**
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
   * Maximum allowed number of managed zones in the project.
   *
   * @param int $managedZones
   */
  public function setManagedZones($managedZones)
  {
    $this->managedZones = $managedZones;
  }
  /**
   * @return int
   */
  public function getManagedZones()
  {
    return $this->managedZones;
  }
  /**
   * Maximum allowed number of managed zones which can be attached to a GKE
   * cluster.
   *
   * @param int $managedZonesPerGkeCluster
   */
  public function setManagedZonesPerGkeCluster($managedZonesPerGkeCluster)
  {
    $this->managedZonesPerGkeCluster = $managedZonesPerGkeCluster;
  }
  /**
   * @return int
   */
  public function getManagedZonesPerGkeCluster()
  {
    return $this->managedZonesPerGkeCluster;
  }
  /**
   * Maximum allowed number of managed zones which can be attached to a network.
   *
   * @param int $managedZonesPerNetwork
   */
  public function setManagedZonesPerNetwork($managedZonesPerNetwork)
  {
    $this->managedZonesPerNetwork = $managedZonesPerNetwork;
  }
  /**
   * @return int
   */
  public function getManagedZonesPerNetwork()
  {
    return $this->managedZonesPerNetwork;
  }
  /**
   * Maximum number of nameservers per delegation, meant to prevent abuse
   *
   * @param int $nameserversPerDelegation
   */
  public function setNameserversPerDelegation($nameserversPerDelegation)
  {
    $this->nameserversPerDelegation = $nameserversPerDelegation;
  }
  /**
   * @return int
   */
  public function getNameserversPerDelegation()
  {
    return $this->nameserversPerDelegation;
  }
  /**
   * Maximum allowed number of networks to which a privately scoped zone can be
   * attached.
   *
   * @param int $networksPerManagedZone
   */
  public function setNetworksPerManagedZone($networksPerManagedZone)
  {
    $this->networksPerManagedZone = $networksPerManagedZone;
  }
  /**
   * @return int
   */
  public function getNetworksPerManagedZone()
  {
    return $this->networksPerManagedZone;
  }
  /**
   * Maximum allowed number of networks per policy.
   *
   * @param int $networksPerPolicy
   */
  public function setNetworksPerPolicy($networksPerPolicy)
  {
    $this->networksPerPolicy = $networksPerPolicy;
  }
  /**
   * @return int
   */
  public function getNetworksPerPolicy()
  {
    return $this->networksPerPolicy;
  }
  /**
   * Maximum allowed number of networks per response policy.
   *
   * @param int $networksPerResponsePolicy
   */
  public function setNetworksPerResponsePolicy($networksPerResponsePolicy)
  {
    $this->networksPerResponsePolicy = $networksPerResponsePolicy;
  }
  /**
   * @return int
   */
  public function getNetworksPerResponsePolicy()
  {
    return $this->networksPerResponsePolicy;
  }
  /**
   * Maximum allowed number of consumer peering zones per target network owned
   * by this producer project
   *
   * @param int $peeringZonesPerTargetNetwork
   */
  public function setPeeringZonesPerTargetNetwork($peeringZonesPerTargetNetwork)
  {
    $this->peeringZonesPerTargetNetwork = $peeringZonesPerTargetNetwork;
  }
  /**
   * @return int
   */
  public function getPeeringZonesPerTargetNetwork()
  {
    return $this->peeringZonesPerTargetNetwork;
  }
  /**
   * Maximum allowed number of policies per project.
   *
   * @param int $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return int
   */
  public function getPolicies()
  {
    return $this->policies;
  }
  /**
   * Maximum allowed number of ResourceRecords per ResourceRecordSet.
   *
   * @param int $resourceRecordsPerRrset
   */
  public function setResourceRecordsPerRrset($resourceRecordsPerRrset)
  {
    $this->resourceRecordsPerRrset = $resourceRecordsPerRrset;
  }
  /**
   * @return int
   */
  public function getResourceRecordsPerRrset()
  {
    return $this->resourceRecordsPerRrset;
  }
  /**
   * Maximum allowed number of response policies per project.
   *
   * @param int $responsePolicies
   */
  public function setResponsePolicies($responsePolicies)
  {
    $this->responsePolicies = $responsePolicies;
  }
  /**
   * @return int
   */
  public function getResponsePolicies()
  {
    return $this->responsePolicies;
  }
  /**
   * Maximum allowed number of rules per response policy.
   *
   * @param int $responsePolicyRulesPerResponsePolicy
   */
  public function setResponsePolicyRulesPerResponsePolicy($responsePolicyRulesPerResponsePolicy)
  {
    $this->responsePolicyRulesPerResponsePolicy = $responsePolicyRulesPerResponsePolicy;
  }
  /**
   * @return int
   */
  public function getResponsePolicyRulesPerResponsePolicy()
  {
    return $this->responsePolicyRulesPerResponsePolicy;
  }
  /**
   * Maximum allowed number of ResourceRecordSets to add per
   * ChangesCreateRequest.
   *
   * @param int $rrsetAdditionsPerChange
   */
  public function setRrsetAdditionsPerChange($rrsetAdditionsPerChange)
  {
    $this->rrsetAdditionsPerChange = $rrsetAdditionsPerChange;
  }
  /**
   * @return int
   */
  public function getRrsetAdditionsPerChange()
  {
    return $this->rrsetAdditionsPerChange;
  }
  /**
   * Maximum allowed number of ResourceRecordSets to delete per
   * ChangesCreateRequest.
   *
   * @param int $rrsetDeletionsPerChange
   */
  public function setRrsetDeletionsPerChange($rrsetDeletionsPerChange)
  {
    $this->rrsetDeletionsPerChange = $rrsetDeletionsPerChange;
  }
  /**
   * @return int
   */
  public function getRrsetDeletionsPerChange()
  {
    return $this->rrsetDeletionsPerChange;
  }
  /**
   * Maximum allowed number of ResourceRecordSets per zone in the project.
   *
   * @param int $rrsetsPerManagedZone
   */
  public function setRrsetsPerManagedZone($rrsetsPerManagedZone)
  {
    $this->rrsetsPerManagedZone = $rrsetsPerManagedZone;
  }
  /**
   * @return int
   */
  public function getRrsetsPerManagedZone()
  {
    return $this->rrsetsPerManagedZone;
  }
  /**
   * Maximum allowed number of target name servers per managed forwarding zone.
   *
   * @param int $targetNameServersPerManagedZone
   */
  public function setTargetNameServersPerManagedZone($targetNameServersPerManagedZone)
  {
    $this->targetNameServersPerManagedZone = $targetNameServersPerManagedZone;
  }
  /**
   * @return int
   */
  public function getTargetNameServersPerManagedZone()
  {
    return $this->targetNameServersPerManagedZone;
  }
  /**
   * Maximum allowed number of alternative target name servers per policy.
   *
   * @param int $targetNameServersPerPolicy
   */
  public function setTargetNameServersPerPolicy($targetNameServersPerPolicy)
  {
    $this->targetNameServersPerPolicy = $targetNameServersPerPolicy;
  }
  /**
   * @return int
   */
  public function getTargetNameServersPerPolicy()
  {
    return $this->targetNameServersPerPolicy;
  }
  /**
   * Maximum allowed size for total rrdata in one ChangesCreateRequest in bytes.
   *
   * @param int $totalRrdataSizePerChange
   */
  public function setTotalRrdataSizePerChange($totalRrdataSizePerChange)
  {
    $this->totalRrdataSizePerChange = $totalRrdataSizePerChange;
  }
  /**
   * @return int
   */
  public function getTotalRrdataSizePerChange()
  {
    return $this->totalRrdataSizePerChange;
  }
  /**
   * DNSSEC algorithm and key length types that can be used for DnsKeys.
   *
   * @param DnsKeySpec[] $whitelistedKeySpecs
   */
  public function setWhitelistedKeySpecs($whitelistedKeySpecs)
  {
    $this->whitelistedKeySpecs = $whitelistedKeySpecs;
  }
  /**
   * @return DnsKeySpec[]
   */
  public function getWhitelistedKeySpecs()
  {
    return $this->whitelistedKeySpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Quota::class, 'Google_Service_Dns_Quota');
