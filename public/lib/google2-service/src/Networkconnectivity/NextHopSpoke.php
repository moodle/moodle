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

class NextHopSpoke extends \Google\Model
{
  /**
   * Indicates whether site-to-site data transfer is allowed for this spoke
   * resource. Data transfer is available only in [supported
   * locations](https://cloud.google.com/network-connectivity/docs/network-
   * connectivity-center/concepts/locations). Whether this route is accessible
   * to other hybrid spokes with site-to-site data transfer enabled. If this is
   * false, the route is only accessible to VPC spokes of the connected Hub.
   *
   * @var bool
   */
  public $siteToSiteDataTransfer;
  /**
   * The URI of the spoke resource.
   *
   * @var string
   */
  public $uri;

  /**
   * Indicates whether site-to-site data transfer is allowed for this spoke
   * resource. Data transfer is available only in [supported
   * locations](https://cloud.google.com/network-connectivity/docs/network-
   * connectivity-center/concepts/locations). Whether this route is accessible
   * to other hybrid spokes with site-to-site data transfer enabled. If this is
   * false, the route is only accessible to VPC spokes of the connected Hub.
   *
   * @param bool $siteToSiteDataTransfer
   */
  public function setSiteToSiteDataTransfer($siteToSiteDataTransfer)
  {
    $this->siteToSiteDataTransfer = $siteToSiteDataTransfer;
  }
  /**
   * @return bool
   */
  public function getSiteToSiteDataTransfer()
  {
    return $this->siteToSiteDataTransfer;
  }
  /**
   * The URI of the spoke resource.
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
class_alias(NextHopSpoke::class, 'Google_Service_Networkconnectivity_NextHopSpoke');
