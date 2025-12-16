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

class SubnetworkUtilizationDetails extends \Google\Collection
{
  protected $collection_key = 'ipv4Utilizations';
  protected $externalIpv6InstanceUtilizationType = SubnetworkUtilizationDetailsIPV6Utilization::class;
  protected $externalIpv6InstanceUtilizationDataType = '';
  protected $externalIpv6LbUtilizationType = SubnetworkUtilizationDetailsIPV6Utilization::class;
  protected $externalIpv6LbUtilizationDataType = '';
  protected $internalIpv6UtilizationType = SubnetworkUtilizationDetailsIPV6Utilization::class;
  protected $internalIpv6UtilizationDataType = '';
  protected $ipv4UtilizationsType = SubnetworkUtilizationDetailsIPV4Utilization::class;
  protected $ipv4UtilizationsDataType = 'array';

  /**
   * Utilizations of external IPV6 IP range.
   *
   * @param SubnetworkUtilizationDetailsIPV6Utilization $externalIpv6InstanceUtilization
   */
  public function setExternalIpv6InstanceUtilization(SubnetworkUtilizationDetailsIPV6Utilization $externalIpv6InstanceUtilization)
  {
    $this->externalIpv6InstanceUtilization = $externalIpv6InstanceUtilization;
  }
  /**
   * @return SubnetworkUtilizationDetailsIPV6Utilization
   */
  public function getExternalIpv6InstanceUtilization()
  {
    return $this->externalIpv6InstanceUtilization;
  }
  /**
   * Utilizations of external IPV6 IP range for NetLB.
   *
   * @param SubnetworkUtilizationDetailsIPV6Utilization $externalIpv6LbUtilization
   */
  public function setExternalIpv6LbUtilization(SubnetworkUtilizationDetailsIPV6Utilization $externalIpv6LbUtilization)
  {
    $this->externalIpv6LbUtilization = $externalIpv6LbUtilization;
  }
  /**
   * @return SubnetworkUtilizationDetailsIPV6Utilization
   */
  public function getExternalIpv6LbUtilization()
  {
    return $this->externalIpv6LbUtilization;
  }
  /**
   * Utilizations of internal IPV6 IP range.
   *
   * @param SubnetworkUtilizationDetailsIPV6Utilization $internalIpv6Utilization
   */
  public function setInternalIpv6Utilization(SubnetworkUtilizationDetailsIPV6Utilization $internalIpv6Utilization)
  {
    $this->internalIpv6Utilization = $internalIpv6Utilization;
  }
  /**
   * @return SubnetworkUtilizationDetailsIPV6Utilization
   */
  public function getInternalIpv6Utilization()
  {
    return $this->internalIpv6Utilization;
  }
  /**
   * Utilizations of all IPV4 IP ranges. For primary ranges, the range name will
   * be empty.
   *
   * @param SubnetworkUtilizationDetailsIPV4Utilization[] $ipv4Utilizations
   */
  public function setIpv4Utilizations($ipv4Utilizations)
  {
    $this->ipv4Utilizations = $ipv4Utilizations;
  }
  /**
   * @return SubnetworkUtilizationDetailsIPV4Utilization[]
   */
  public function getIpv4Utilizations()
  {
    return $this->ipv4Utilizations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworkUtilizationDetails::class, 'Google_Service_Compute_SubnetworkUtilizationDetails');
