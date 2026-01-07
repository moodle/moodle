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

class CloudSecurityZerotrustApplinkAppConnectorProtoGateway extends \Google\Model
{
  /**
   * interface specifies the network interface of the gateway to connect to.
   *
   * @var string
   */
  public $interface;
  /**
   * name is the name of an instance running a gateway. It is the unique ID for
   * a gateway. All gateways under the same connection have the same prefix. It
   * is derived from the gateway URL. For example, name=${instance} assuming a
   * gateway URL. https://www.googleapis.com/compute/${version}/projects/${proje
   * ct}/zones/${zone}/instances/${instance}
   *
   * @var string
   */
  public $name;
  /**
   * port specifies the port of the gateway for tunnel connections from the
   * connectors.
   *
   * @var string
   */
  public $port;
  /**
   * project is the tenant project the gateway belongs to. Different from the
   * project in the connection, it is a BeyondCorpAPI internally created project
   * to manage all the gateways. It is sharing the same network with the
   * consumer project user owned. It is derived from the gateway URL. For
   * example, project=${project} assuming a gateway URL. https://www.googleapis.
   * com/compute/${version}/projects/${project}/zones/${zone}/instances/${instan
   * ce}
   *
   * @var string
   */
  public $project;
  /**
   * self_link is the gateway URL in the form https://www.googleapis.com/compute
   * /${version}/projects/${project}/zones/${zone}/instances/${instance}
   *
   * @var string
   */
  public $selfLink;
  /**
   * zone represents the zone the instance belongs. It is derived from the
   * gateway URL. For example, zone=${zone} assuming a gateway URL. https://www.
   * googleapis.com/compute/${version}/projects/${project}/zones/${zone}/instanc
   * es/${instance}
   *
   * @var string
   */
  public $zone;

  /**
   * interface specifies the network interface of the gateway to connect to.
   *
   * @param string $interface
   */
  public function setInterface($interface)
  {
    $this->interface = $interface;
  }
  /**
   * @return string
   */
  public function getInterface()
  {
    return $this->interface;
  }
  /**
   * name is the name of an instance running a gateway. It is the unique ID for
   * a gateway. All gateways under the same connection have the same prefix. It
   * is derived from the gateway URL. For example, name=${instance} assuming a
   * gateway URL. https://www.googleapis.com/compute/${version}/projects/${proje
   * ct}/zones/${zone}/instances/${instance}
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
  /**
   * port specifies the port of the gateway for tunnel connections from the
   * connectors.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * project is the tenant project the gateway belongs to. Different from the
   * project in the connection, it is a BeyondCorpAPI internally created project
   * to manage all the gateways. It is sharing the same network with the
   * consumer project user owned. It is derived from the gateway URL. For
   * example, project=${project} assuming a gateway URL. https://www.googleapis.
   * com/compute/${version}/projects/${project}/zones/${zone}/instances/${instan
   * ce}
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * self_link is the gateway URL in the form https://www.googleapis.com/compute
   * /${version}/projects/${project}/zones/${zone}/instances/${instance}
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * zone represents the zone the instance belongs. It is derived from the
   * gateway URL. For example, zone=${zone} assuming a gateway URL. https://www.
   * googleapis.com/compute/${version}/projects/${project}/zones/${zone}/instanc
   * es/${instance}
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSecurityZerotrustApplinkAppConnectorProtoGateway::class, 'Google_Service_BeyondCorp_CloudSecurityZerotrustApplinkAppConnectorProtoGateway');
