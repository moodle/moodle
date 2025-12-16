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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2VpcAccess extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const EGRESS_VPC_EGRESS_UNSPECIFIED = 'VPC_EGRESS_UNSPECIFIED';
  /**
   * All outbound traffic is routed through the VPC connector.
   */
  public const EGRESS_ALL_TRAFFIC = 'ALL_TRAFFIC';
  /**
   * Only private IP ranges are routed through the VPC connector.
   */
  public const EGRESS_PRIVATE_RANGES_ONLY = 'PRIVATE_RANGES_ONLY';
  protected $collection_key = 'networkInterfaces';
  /**
   * VPC Access connector name. Format:
   * `projects/{project}/locations/{location}/connectors/{connector}`, where
   * `{project}` can be project id or number. For more information on sending
   * traffic to a VPC network via a connector, visit
   * https://cloud.google.com/run/docs/configuring/vpc-connectors.
   *
   * @var string
   */
  public $connector;
  /**
   * Optional. Traffic VPC egress settings. If not provided, it defaults to
   * PRIVATE_RANGES_ONLY.
   *
   * @var string
   */
  public $egress;
  protected $networkInterfacesType = GoogleCloudRunV2NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';

  /**
   * VPC Access connector name. Format:
   * `projects/{project}/locations/{location}/connectors/{connector}`, where
   * `{project}` can be project id or number. For more information on sending
   * traffic to a VPC network via a connector, visit
   * https://cloud.google.com/run/docs/configuring/vpc-connectors.
   *
   * @param string $connector
   */
  public function setConnector($connector)
  {
    $this->connector = $connector;
  }
  /**
   * @return string
   */
  public function getConnector()
  {
    return $this->connector;
  }
  /**
   * Optional. Traffic VPC egress settings. If not provided, it defaults to
   * PRIVATE_RANGES_ONLY.
   *
   * Accepted values: VPC_EGRESS_UNSPECIFIED, ALL_TRAFFIC, PRIVATE_RANGES_ONLY
   *
   * @param self::EGRESS_* $egress
   */
  public function setEgress($egress)
  {
    $this->egress = $egress;
  }
  /**
   * @return self::EGRESS_*
   */
  public function getEgress()
  {
    return $this->egress;
  }
  /**
   * Optional. Direct VPC egress settings. Currently only single network
   * interface is supported.
   *
   * @param GoogleCloudRunV2NetworkInterface[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return GoogleCloudRunV2NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2VpcAccess::class, 'Google_Service_CloudRun_GoogleCloudRunV2VpcAccess');
