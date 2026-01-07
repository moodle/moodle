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

class UpdateDnsRecordSetRequest extends \Google\Model
{
  /**
   * Required. The network that the consumer is using to connect with services.
   * Must be in the form of projects/{project}/global/networks/{network}
   * {project} is the project number, as in '12345' {network} is the network
   * name.
   *
   * @var string
   */
  public $consumerNetwork;
  protected $existingDnsRecordSetType = DnsRecordSet::class;
  protected $existingDnsRecordSetDataType = '';
  protected $newDnsRecordSetType = DnsRecordSet::class;
  protected $newDnsRecordSetDataType = '';
  /**
   * Required. The name of the private DNS zone in the shared producer host
   * project from which the record set will be removed.
   *
   * @var string
   */
  public $zone;

  /**
   * Required. The network that the consumer is using to connect with services.
   * Must be in the form of projects/{project}/global/networks/{network}
   * {project} is the project number, as in '12345' {network} is the network
   * name.
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
   * Required. The existing DNS record set to update.
   *
   * @param DnsRecordSet $existingDnsRecordSet
   */
  public function setExistingDnsRecordSet(DnsRecordSet $existingDnsRecordSet)
  {
    $this->existingDnsRecordSet = $existingDnsRecordSet;
  }
  /**
   * @return DnsRecordSet
   */
  public function getExistingDnsRecordSet()
  {
    return $this->existingDnsRecordSet;
  }
  /**
   * Required. The new values that the DNS record set should be updated to hold.
   *
   * @param DnsRecordSet $newDnsRecordSet
   */
  public function setNewDnsRecordSet(DnsRecordSet $newDnsRecordSet)
  {
    $this->newDnsRecordSet = $newDnsRecordSet;
  }
  /**
   * @return DnsRecordSet
   */
  public function getNewDnsRecordSet()
  {
    return $this->newDnsRecordSet;
  }
  /**
   * Required. The name of the private DNS zone in the shared producer host
   * project from which the record set will be removed.
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
class_alias(UpdateDnsRecordSetRequest::class, 'Google_Service_ServiceNetworking_UpdateDnsRecordSetRequest');
