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

namespace Google\Service\Appengine;

class VpcAccessConnector extends \Google\Model
{
  public const EGRESS_SETTING_EGRESS_SETTING_UNSPECIFIED = 'EGRESS_SETTING_UNSPECIFIED';
  /**
   * Force the use of VPC Access for all egress traffic from the function.
   */
  public const EGRESS_SETTING_ALL_TRAFFIC = 'ALL_TRAFFIC';
  /**
   * Use the VPC Access Connector for private IP space from RFC1918.
   */
  public const EGRESS_SETTING_PRIVATE_IP_RANGES = 'PRIVATE_IP_RANGES';
  /**
   * The egress setting for the connector, controlling what traffic is diverted
   * through it.
   *
   * @var string
   */
  public $egressSetting;
  /**
   * Full Serverless VPC Access Connector name e.g. projects/my-
   * project/locations/us-central1/connectors/c1.
   *
   * @var string
   */
  public $name;

  /**
   * The egress setting for the connector, controlling what traffic is diverted
   * through it.
   *
   * Accepted values: EGRESS_SETTING_UNSPECIFIED, ALL_TRAFFIC, PRIVATE_IP_RANGES
   *
   * @param self::EGRESS_SETTING_* $egressSetting
   */
  public function setEgressSetting($egressSetting)
  {
    $this->egressSetting = $egressSetting;
  }
  /**
   * @return self::EGRESS_SETTING_*
   */
  public function getEgressSetting()
  {
    return $this->egressSetting;
  }
  /**
   * Full Serverless VPC Access Connector name e.g. projects/my-
   * project/locations/us-central1/connectors/c1.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcAccessConnector::class, 'Google_Service_Appengine_VpcAccessConnector');
