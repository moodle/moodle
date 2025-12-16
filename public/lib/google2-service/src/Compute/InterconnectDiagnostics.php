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

class InterconnectDiagnostics extends \Google\Collection
{
  /**
   * LACP is enabled.
   */
  public const BUNDLE_AGGREGATION_TYPE_BUNDLE_AGGREGATION_TYPE_LACP = 'BUNDLE_AGGREGATION_TYPE_LACP';
  /**
   * LACP is disabled.
   */
  public const BUNDLE_AGGREGATION_TYPE_BUNDLE_AGGREGATION_TYPE_STATIC = 'BUNDLE_AGGREGATION_TYPE_STATIC';
  /**
   * If bundleAggregationType is LACP: LACP is not established and/or all links
   * in the bundle have DOWN operational status. If bundleAggregationType is
   * STATIC: one or more links in the bundle has DOWN operational status.
   */
  public const BUNDLE_OPERATIONAL_STATUS_BUNDLE_OPERATIONAL_STATUS_DOWN = 'BUNDLE_OPERATIONAL_STATUS_DOWN';
  /**
   * If bundleAggregationType is LACP: LACP is established and at least one link
   * in the bundle has UP operational status. If bundleAggregationType is
   * STATIC: all links in the bundle (typically just one) have UP operational
   * status.
   */
  public const BUNDLE_OPERATIONAL_STATUS_BUNDLE_OPERATIONAL_STATUS_UP = 'BUNDLE_OPERATIONAL_STATUS_UP';
  protected $collection_key = 'links';
  protected $arpCachesType = InterconnectDiagnosticsARPEntry::class;
  protected $arpCachesDataType = 'array';
  /**
   * The aggregation type of the bundle interface.
   *
   * @var string
   */
  public $bundleAggregationType;
  /**
   * The operational status of the bundle interface.
   *
   * @var string
   */
  public $bundleOperationalStatus;
  protected $linksType = InterconnectDiagnosticsLinkStatus::class;
  protected $linksDataType = 'array';
  /**
   * The MAC address of the Interconnect's bundle interface.
   *
   * @var string
   */
  public $macAddress;

  /**
   * A list of InterconnectDiagnostics.ARPEntry objects, describing individual
   * neighbors currently seen by the Google router in the ARP cache for the
   * Interconnect. This will be empty when the Interconnect is not bundled.
   *
   * @param InterconnectDiagnosticsARPEntry[] $arpCaches
   */
  public function setArpCaches($arpCaches)
  {
    $this->arpCaches = $arpCaches;
  }
  /**
   * @return InterconnectDiagnosticsARPEntry[]
   */
  public function getArpCaches()
  {
    return $this->arpCaches;
  }
  /**
   * The aggregation type of the bundle interface.
   *
   * Accepted values: BUNDLE_AGGREGATION_TYPE_LACP,
   * BUNDLE_AGGREGATION_TYPE_STATIC
   *
   * @param self::BUNDLE_AGGREGATION_TYPE_* $bundleAggregationType
   */
  public function setBundleAggregationType($bundleAggregationType)
  {
    $this->bundleAggregationType = $bundleAggregationType;
  }
  /**
   * @return self::BUNDLE_AGGREGATION_TYPE_*
   */
  public function getBundleAggregationType()
  {
    return $this->bundleAggregationType;
  }
  /**
   * The operational status of the bundle interface.
   *
   * Accepted values: BUNDLE_OPERATIONAL_STATUS_DOWN,
   * BUNDLE_OPERATIONAL_STATUS_UP
   *
   * @param self::BUNDLE_OPERATIONAL_STATUS_* $bundleOperationalStatus
   */
  public function setBundleOperationalStatus($bundleOperationalStatus)
  {
    $this->bundleOperationalStatus = $bundleOperationalStatus;
  }
  /**
   * @return self::BUNDLE_OPERATIONAL_STATUS_*
   */
  public function getBundleOperationalStatus()
  {
    return $this->bundleOperationalStatus;
  }
  /**
   * A list of InterconnectDiagnostics.LinkStatus objects, describing the status
   * for each link on the Interconnect.
   *
   * @param InterconnectDiagnosticsLinkStatus[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return InterconnectDiagnosticsLinkStatus[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * The MAC address of the Interconnect's bundle interface.
   *
   * @param string $macAddress
   */
  public function setMacAddress($macAddress)
  {
    $this->macAddress = $macAddress;
  }
  /**
   * @return string
   */
  public function getMacAddress()
  {
    return $this->macAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectDiagnostics::class, 'Google_Service_Compute_InterconnectDiagnostics');
