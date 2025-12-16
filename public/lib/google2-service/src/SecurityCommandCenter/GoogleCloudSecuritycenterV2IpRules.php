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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2IpRules extends \Google\Collection
{
  /**
   * Unspecified direction value.
   */
  public const DIRECTION_DIRECTION_UNSPECIFIED = 'DIRECTION_UNSPECIFIED';
  /**
   * Ingress direction value.
   */
  public const DIRECTION_INGRESS = 'INGRESS';
  /**
   * Egress direction value.
   */
  public const DIRECTION_EGRESS = 'EGRESS';
  protected $collection_key = 'sourceIpRanges';
  protected $allowedType = GoogleCloudSecuritycenterV2Allowed::class;
  protected $allowedDataType = '';
  protected $deniedType = GoogleCloudSecuritycenterV2Denied::class;
  protected $deniedDataType = '';
  /**
   * If destination IP ranges are specified, the firewall rule applies only to
   * traffic that has a destination IP address in these ranges. These ranges
   * must be expressed in CIDR format. Only supports IPv4.
   *
   * @var string[]
   */
  public $destinationIpRanges;
  /**
   * The direction that the rule is applicable to, one of ingress or egress.
   *
   * @var string
   */
  public $direction;
  /**
   * Name of the network protocol service, such as FTP, that is exposed by the
   * open port. Follows the naming convention available at:
   * https://www.iana.org/assignments/service-names-port-numbers/service-names-
   * port-numbers.xhtml.
   *
   * @var string[]
   */
  public $exposedServices;
  /**
   * If source IP ranges are specified, the firewall rule applies only to
   * traffic that has a source IP address in these ranges. These ranges must be
   * expressed in CIDR format. Only supports IPv4.
   *
   * @var string[]
   */
  public $sourceIpRanges;

  /**
   * Tuple with allowed rules.
   *
   * @param GoogleCloudSecuritycenterV2Allowed $allowed
   */
  public function setAllowed(GoogleCloudSecuritycenterV2Allowed $allowed)
  {
    $this->allowed = $allowed;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Allowed
   */
  public function getAllowed()
  {
    return $this->allowed;
  }
  /**
   * Tuple with denied rules.
   *
   * @param GoogleCloudSecuritycenterV2Denied $denied
   */
  public function setDenied(GoogleCloudSecuritycenterV2Denied $denied)
  {
    $this->denied = $denied;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Denied
   */
  public function getDenied()
  {
    return $this->denied;
  }
  /**
   * If destination IP ranges are specified, the firewall rule applies only to
   * traffic that has a destination IP address in these ranges. These ranges
   * must be expressed in CIDR format. Only supports IPv4.
   *
   * @param string[] $destinationIpRanges
   */
  public function setDestinationIpRanges($destinationIpRanges)
  {
    $this->destinationIpRanges = $destinationIpRanges;
  }
  /**
   * @return string[]
   */
  public function getDestinationIpRanges()
  {
    return $this->destinationIpRanges;
  }
  /**
   * The direction that the rule is applicable to, one of ingress or egress.
   *
   * Accepted values: DIRECTION_UNSPECIFIED, INGRESS, EGRESS
   *
   * @param self::DIRECTION_* $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return self::DIRECTION_*
   */
  public function getDirection()
  {
    return $this->direction;
  }
  /**
   * Name of the network protocol service, such as FTP, that is exposed by the
   * open port. Follows the naming convention available at:
   * https://www.iana.org/assignments/service-names-port-numbers/service-names-
   * port-numbers.xhtml.
   *
   * @param string[] $exposedServices
   */
  public function setExposedServices($exposedServices)
  {
    $this->exposedServices = $exposedServices;
  }
  /**
   * @return string[]
   */
  public function getExposedServices()
  {
    return $this->exposedServices;
  }
  /**
   * If source IP ranges are specified, the firewall rule applies only to
   * traffic that has a source IP address in these ranges. These ranges must be
   * expressed in CIDR format. Only supports IPv4.
   *
   * @param string[] $sourceIpRanges
   */
  public function setSourceIpRanges($sourceIpRanges)
  {
    $this->sourceIpRanges = $sourceIpRanges;
  }
  /**
   * @return string[]
   */
  public function getSourceIpRanges()
  {
    return $this->sourceIpRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IpRules::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IpRules');
