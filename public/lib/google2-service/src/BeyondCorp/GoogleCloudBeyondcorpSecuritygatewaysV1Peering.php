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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpSecuritygatewaysV1Peering extends \Google\Collection
{
  protected $collection_key = 'dnsZones';
  /**
   * @var string[]
   */
  public $dnsZones;
  /**
   * @var string
   */
  public $targetNetwork;

  /**
   * @param string[]
   */
  public function setDnsZones($dnsZones)
  {
    $this->dnsZones = $dnsZones;
  }
  /**
   * @return string[]
   */
  public function getDnsZones()
  {
    return $this->dnsZones;
  }
  /**
   * @param string
   */
  public function setTargetNetwork($targetNetwork)
  {
    $this->targetNetwork = $targetNetwork;
  }
  /**
   * @return string
   */
  public function getTargetNetwork()
  {
    return $this->targetNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1Peering::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1Peering');
