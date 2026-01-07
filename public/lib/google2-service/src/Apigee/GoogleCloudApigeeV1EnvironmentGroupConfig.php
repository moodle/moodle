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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1EnvironmentGroupConfig extends \Google\Collection
{
  protected $collection_key = 'routingRules';
  protected $endpointChainingRulesType = GoogleCloudApigeeV1EndpointChainingRule::class;
  protected $endpointChainingRulesDataType = 'array';
  /**
   * Host names for the environment group.
   *
   * @var string[]
   */
  public $hostnames;
  /**
   * When this message appears in the top-level IngressConfig, this field will
   * be populated in lieu of the inlined routing_rules and hostnames fields.
   * Some URL for downloading the full EnvironmentGroupConfig for this group.
   *
   * @var string
   */
  public $location;
  /**
   * Name of the environment group in the following format:
   * `organizations/{org}/envgroups/{envgroup}`.
   *
   * @var string
   */
  public $name;
  /**
   * Revision id that defines the ordering of the EnvironmentGroupConfig
   * resource. The higher the revision, the more recently the configuration was
   * deployed.
   *
   * @var string
   */
  public $revisionId;
  protected $routingRulesType = GoogleCloudApigeeV1RoutingRule::class;
  protected $routingRulesDataType = 'array';
  /**
   * A unique id for the environment group config that will only change if the
   * environment group is deleted and recreated.
   *
   * @var string
   */
  public $uid;

  /**
   * A list of proxies in each deployment group for proxy chaining calls.
   *
   * @param GoogleCloudApigeeV1EndpointChainingRule[] $endpointChainingRules
   */
  public function setEndpointChainingRules($endpointChainingRules)
  {
    $this->endpointChainingRules = $endpointChainingRules;
  }
  /**
   * @return GoogleCloudApigeeV1EndpointChainingRule[]
   */
  public function getEndpointChainingRules()
  {
    return $this->endpointChainingRules;
  }
  /**
   * Host names for the environment group.
   *
   * @param string[] $hostnames
   */
  public function setHostnames($hostnames)
  {
    $this->hostnames = $hostnames;
  }
  /**
   * @return string[]
   */
  public function getHostnames()
  {
    return $this->hostnames;
  }
  /**
   * When this message appears in the top-level IngressConfig, this field will
   * be populated in lieu of the inlined routing_rules and hostnames fields.
   * Some URL for downloading the full EnvironmentGroupConfig for this group.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Name of the environment group in the following format:
   * `organizations/{org}/envgroups/{envgroup}`.
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
   * Revision id that defines the ordering of the EnvironmentGroupConfig
   * resource. The higher the revision, the more recently the configuration was
   * deployed.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Ordered list of routing rules defining how traffic to this environment
   * group's hostnames should be routed to different environments.
   *
   * @param GoogleCloudApigeeV1RoutingRule[] $routingRules
   */
  public function setRoutingRules($routingRules)
  {
    $this->routingRules = $routingRules;
  }
  /**
   * @return GoogleCloudApigeeV1RoutingRule[]
   */
  public function getRoutingRules()
  {
    return $this->routingRules;
  }
  /**
   * A unique id for the environment group config that will only change if the
   * environment group is deleted and recreated.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentGroupConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentGroupConfig');
