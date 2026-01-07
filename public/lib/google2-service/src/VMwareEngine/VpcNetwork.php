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

namespace Google\Service\VMwareEngine;

class VpcNetwork extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * VPC network that will be peered with a consumer VPC network or the intranet
   * VPC of another VMware Engine network. Access a private cloud through
   * Compute Engine VMs on a peered VPC network or an on-premises resource
   * connected to a peered consumer VPC network.
   */
  public const TYPE_INTRANET = 'INTRANET';
  /**
   * VPC network used for internet access to and from a private cloud.
   */
  public const TYPE_INTERNET = 'INTERNET';
  /**
   * VPC network used for access to Google Cloud services like Cloud Storage.
   */
  public const TYPE_GOOGLE_CLOUD = 'GOOGLE_CLOUD';
  /**
   * Output only. The relative resource name of the service VPC network this
   * VMware Engine network is attached to. For example:
   * `projects/123123/global/networks/my-network`
   *
   * @var string
   */
  public $network;
  /**
   * Output only. Type of VPC network (INTRANET, INTERNET, or GOOGLE_CLOUD)
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The relative resource name of the service VPC network this
   * VMware Engine network is attached to. For example:
   * `projects/123123/global/networks/my-network`
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
  /**
   * Output only. Type of VPC network (INTRANET, INTERNET, or GOOGLE_CLOUD)
   *
   * Accepted values: TYPE_UNSPECIFIED, INTRANET, INTERNET, GOOGLE_CLOUD
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcNetwork::class, 'Google_Service_VMwareEngine_VpcNetwork');
