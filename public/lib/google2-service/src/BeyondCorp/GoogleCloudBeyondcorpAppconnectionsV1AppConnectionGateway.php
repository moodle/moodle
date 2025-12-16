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

class GoogleCloudBeyondcorpAppconnectionsV1AppConnectionGateway extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Gateway hosted in a GCP regional managed instance group.
   */
  public const TYPE_GCP_REGIONAL_MIG = 'GCP_REGIONAL_MIG';
  /**
   * Required. AppGateway name in following format:
   * `projects/{project_id}/locations/{location_id}/appgateways/{gateway_id}`
   *
   * @var string
   */
  public $appGateway;
  /**
   * Output only. Ingress port reserved on the gateways for this AppConnection,
   * if not specified or zero, the default port is 19443.
   *
   * @var int
   */
  public $ingressPort;
  /**
   * Output only. L7 private service connection for this resource.
   *
   * @var string
   */
  public $l7psc;
  /**
   * Required. The type of hosting used by the gateway.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Server-defined URI for this resource.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. AppGateway name in following format:
   * `projects/{project_id}/locations/{location_id}/appgateways/{gateway_id}`
   *
   * @param string $appGateway
   */
  public function setAppGateway($appGateway)
  {
    $this->appGateway = $appGateway;
  }
  /**
   * @return string
   */
  public function getAppGateway()
  {
    return $this->appGateway;
  }
  /**
   * Output only. Ingress port reserved on the gateways for this AppConnection,
   * if not specified or zero, the default port is 19443.
   *
   * @param int $ingressPort
   */
  public function setIngressPort($ingressPort)
  {
    $this->ingressPort = $ingressPort;
  }
  /**
   * @return int
   */
  public function getIngressPort()
  {
    return $this->ingressPort;
  }
  /**
   * Output only. L7 private service connection for this resource.
   *
   * @param string $l7psc
   */
  public function setL7psc($l7psc)
  {
    $this->l7psc = $l7psc;
  }
  /**
   * @return string
   */
  public function getL7psc()
  {
    return $this->l7psc;
  }
  /**
   * Required. The type of hosting used by the gateway.
   *
   * Accepted values: TYPE_UNSPECIFIED, GCP_REGIONAL_MIG
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
  /**
   * Output only. Server-defined URI for this resource.
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
class_alias(GoogleCloudBeyondcorpAppconnectionsV1AppConnectionGateway::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectionsV1AppConnectionGateway');
