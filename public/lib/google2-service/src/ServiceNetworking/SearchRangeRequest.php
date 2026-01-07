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

class SearchRangeRequest extends \Google\Model
{
  /**
   * Required. The prefix length of the IP range. Use usual CIDR range notation.
   * For example, '30' to find unused x.x.x.x/30 CIDR range. Actual range will
   * be determined using allocated range for the consumer peered network and
   * returned in the result.
   *
   * @var int
   */
  public $ipPrefixLength;
  /**
   * Required. Network name in the consumer project. This network must have been
   * already peered with a shared VPC network using CreateConnection method.
   * Must be in a form 'projects/{project}/global/networks/{network}'. {project}
   * is a project number, as in '12345' {network} is network name.
   *
   * @var string
   */
  public $network;

  /**
   * Required. The prefix length of the IP range. Use usual CIDR range notation.
   * For example, '30' to find unused x.x.x.x/30 CIDR range. Actual range will
   * be determined using allocated range for the consumer peered network and
   * returned in the result.
   *
   * @param int $ipPrefixLength
   */
  public function setIpPrefixLength($ipPrefixLength)
  {
    $this->ipPrefixLength = $ipPrefixLength;
  }
  /**
   * @return int
   */
  public function getIpPrefixLength()
  {
    return $this->ipPrefixLength;
  }
  /**
   * Required. Network name in the consumer project. This network must have been
   * already peered with a shared VPC network using CreateConnection method.
   * Must be in a form 'projects/{project}/global/networks/{network}'. {project}
   * is a project number, as in '12345' {network} is network name.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchRangeRequest::class, 'Google_Service_ServiceNetworking_SearchRangeRequest');
