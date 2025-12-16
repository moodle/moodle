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

class NatIpInfo extends \Google\Collection
{
  protected $collection_key = 'natIpInfoMappings';
  protected $natIpInfoMappingsType = NatIpInfoNatIpInfoMapping::class;
  protected $natIpInfoMappingsDataType = 'array';
  /**
   * Output only. Name of the NAT config which the NAT IP belongs to.
   *
   * @var string
   */
  public $natName;

  /**
   * Output only. A list of all NAT IPs assigned to this NAT config.
   *
   * @param NatIpInfoNatIpInfoMapping[] $natIpInfoMappings
   */
  public function setNatIpInfoMappings($natIpInfoMappings)
  {
    $this->natIpInfoMappings = $natIpInfoMappings;
  }
  /**
   * @return NatIpInfoNatIpInfoMapping[]
   */
  public function getNatIpInfoMappings()
  {
    return $this->natIpInfoMappings;
  }
  /**
   * Output only. Name of the NAT config which the NAT IP belongs to.
   *
   * @param string $natName
   */
  public function setNatName($natName)
  {
    $this->natName = $natName;
  }
  /**
   * @return string
   */
  public function getNatName()
  {
    return $this->natName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NatIpInfo::class, 'Google_Service_Compute_NatIpInfo');
