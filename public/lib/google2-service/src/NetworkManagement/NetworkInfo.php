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

namespace Google\Service\NetworkManagement;

class NetworkInfo extends \Google\Model
{
  /**
   * Name of a Compute Engine network.
   *
   * @var string
   */
  public $displayName;
  /**
   * The IP range of the subnet matching the source IP address of the test.
   *
   * @var string
   */
  public $matchedIpRange;
  /**
   * URI of the subnet matching the source IP address of the test.
   *
   * @var string
   */
  public $matchedSubnetUri;
  /**
   * The region of the subnet matching the source IP address of the test.
   *
   * @var string
   */
  public $region;
  /**
   * URI of a Compute Engine network.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Compute Engine network.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The IP range of the subnet matching the source IP address of the test.
   *
   * @param string $matchedIpRange
   */
  public function setMatchedIpRange($matchedIpRange)
  {
    $this->matchedIpRange = $matchedIpRange;
  }
  /**
   * @return string
   */
  public function getMatchedIpRange()
  {
    return $this->matchedIpRange;
  }
  /**
   * URI of the subnet matching the source IP address of the test.
   *
   * @param string $matchedSubnetUri
   */
  public function setMatchedSubnetUri($matchedSubnetUri)
  {
    $this->matchedSubnetUri = $matchedSubnetUri;
  }
  /**
   * @return string
   */
  public function getMatchedSubnetUri()
  {
    return $this->matchedSubnetUri;
  }
  /**
   * The region of the subnet matching the source IP address of the test.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * URI of a Compute Engine network.
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
class_alias(NetworkInfo::class, 'Google_Service_NetworkManagement_NetworkInfo');
