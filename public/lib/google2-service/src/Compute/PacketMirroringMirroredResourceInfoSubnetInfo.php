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

class PacketMirroringMirroredResourceInfoSubnetInfo extends \Google\Model
{
  /**
   * Output only. [Output Only] Unique identifier for the subnetwork; defined by
   * the server.
   *
   * @var string
   */
  public $canonicalUrl;
  /**
   * Resource URL to the subnetwork for which traffic from/to all VM instances
   * will be mirrored.
   *
   * @var string
   */
  public $url;

  /**
   * Output only. [Output Only] Unique identifier for the subnetwork; defined by
   * the server.
   *
   * @param string $canonicalUrl
   */
  public function setCanonicalUrl($canonicalUrl)
  {
    $this->canonicalUrl = $canonicalUrl;
  }
  /**
   * @return string
   */
  public function getCanonicalUrl()
  {
    return $this->canonicalUrl;
  }
  /**
   * Resource URL to the subnetwork for which traffic from/to all VM instances
   * will be mirrored.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PacketMirroringMirroredResourceInfoSubnetInfo::class, 'Google_Service_Compute_PacketMirroringMirroredResourceInfoSubnetInfo');
