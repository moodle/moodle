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

namespace Google\Service\Networkconnectivity;

class RoutingVPC extends \Google\Model
{
  /**
   * Output only. If true, indicates that this VPC network is currently
   * associated with spokes that use the data transfer feature (spokes where the
   * site_to_site_data_transfer field is set to true). If you create new spokes
   * that use data transfer, they must be associated with this VPC network. At
   * most, one VPC network will have this field set to true.
   *
   * @var bool
   */
  public $requiredForNewSiteToSiteDataTransferSpokes;
  /**
   * The URI of the VPC network.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. If true, indicates that this VPC network is currently
   * associated with spokes that use the data transfer feature (spokes where the
   * site_to_site_data_transfer field is set to true). If you create new spokes
   * that use data transfer, they must be associated with this VPC network. At
   * most, one VPC network will have this field set to true.
   *
   * @param bool $requiredForNewSiteToSiteDataTransferSpokes
   */
  public function setRequiredForNewSiteToSiteDataTransferSpokes($requiredForNewSiteToSiteDataTransferSpokes)
  {
    $this->requiredForNewSiteToSiteDataTransferSpokes = $requiredForNewSiteToSiteDataTransferSpokes;
  }
  /**
   * @return bool
   */
  public function getRequiredForNewSiteToSiteDataTransferSpokes()
  {
    return $this->requiredForNewSiteToSiteDataTransferSpokes;
  }
  /**
   * The URI of the VPC network.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoutingVPC::class, 'Google_Service_Networkconnectivity_RoutingVPC');
