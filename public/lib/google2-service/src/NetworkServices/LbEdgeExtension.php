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

namespace Google\Service\NetworkServices;

class LbEdgeExtension extends \Google\Collection
{
  /**
   * Default value. Do not use.
   */
  public const LOAD_BALANCING_SCHEME_LOAD_BALANCING_SCHEME_UNSPECIFIED = 'LOAD_BALANCING_SCHEME_UNSPECIFIED';
  /**
   * Signifies that this is used for Internal HTTP(S) Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  /**
   * Signifies that this is used for External Managed HTTP(S) Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_EXTERNAL_MANAGED = 'EXTERNAL_MANAGED';
  protected $collection_key = 'forwardingRules';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A human-readable description of the resource.
   *
   * @var string
   */
  public $description;
  protected $extensionChainsType = ExtensionChain::class;
  protected $extensionChainsDataType = 'array';
  /**
   * Required. A list of references to the forwarding rules to which this
   * service extension is attached. At least one forwarding rule is required.
   * Only one `LbEdgeExtension` resource can be associated with a forwarding
   * rule.
   *
   * @var string[]
   */
  public $forwardingRules;
  /**
   * Optional. Set of labels associated with the `LbEdgeExtension` resource. The
   * format must comply with [the requirements for
   * labels](https://cloud.google.com/compute/docs/labeling-
   * resources#requirements) for Google Cloud resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. All forwarding rules referenced by this extension must share the
   * same load balancing scheme. Supported values: `EXTERNAL_MANAGED`.
   *
   * @var string
   */
  public $loadBalancingScheme;
  /**
   * Required. Identifier. Name of the `LbEdgeExtension` resource in the
   * following format: `projects/{project}/locations/{location}/lbEdgeExtensions
   * /{lb_edge_extension}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A human-readable description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. A set of ordered extension chains that contain the match
   * conditions and extensions to execute. Match conditions for each extension
   * chain are evaluated in sequence for a given request. The first extension
   * chain that has a condition that matches the request is executed. Any
   * subsequent extension chains do not execute. Limited to 5 extension chains
   * per resource.
   *
   * @param ExtensionChain[] $extensionChains
   */
  public function setExtensionChains($extensionChains)
  {
    $this->extensionChains = $extensionChains;
  }
  /**
   * @return ExtensionChain[]
   */
  public function getExtensionChains()
  {
    return $this->extensionChains;
  }
  /**
   * Required. A list of references to the forwarding rules to which this
   * service extension is attached. At least one forwarding rule is required.
   * Only one `LbEdgeExtension` resource can be associated with a forwarding
   * rule.
   *
   * @param string[] $forwardingRules
   */
  public function setForwardingRules($forwardingRules)
  {
    $this->forwardingRules = $forwardingRules;
  }
  /**
   * @return string[]
   */
  public function getForwardingRules()
  {
    return $this->forwardingRules;
  }
  /**
   * Optional. Set of labels associated with the `LbEdgeExtension` resource. The
   * format must comply with [the requirements for
   * labels](https://cloud.google.com/compute/docs/labeling-
   * resources#requirements) for Google Cloud resources.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. All forwarding rules referenced by this extension must share the
   * same load balancing scheme. Supported values: `EXTERNAL_MANAGED`.
   *
   * Accepted values: LOAD_BALANCING_SCHEME_UNSPECIFIED, INTERNAL_MANAGED,
   * EXTERNAL_MANAGED
   *
   * @param self::LOAD_BALANCING_SCHEME_* $loadBalancingScheme
   */
  public function setLoadBalancingScheme($loadBalancingScheme)
  {
    $this->loadBalancingScheme = $loadBalancingScheme;
  }
  /**
   * @return self::LOAD_BALANCING_SCHEME_*
   */
  public function getLoadBalancingScheme()
  {
    return $this->loadBalancingScheme;
  }
  /**
   * Required. Identifier. Name of the `LbEdgeExtension` resource in the
   * following format: `projects/{project}/locations/{location}/lbEdgeExtensions
   * /{lb_edge_extension}`.
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
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LbEdgeExtension::class, 'Google_Service_NetworkServices_LbEdgeExtension');
