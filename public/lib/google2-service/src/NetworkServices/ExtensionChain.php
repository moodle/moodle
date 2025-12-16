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

class ExtensionChain extends \Google\Collection
{
  protected $collection_key = 'extensions';
  protected $extensionsType = ExtensionChainExtension::class;
  protected $extensionsDataType = 'array';
  protected $matchConditionType = ExtensionChainMatchCondition::class;
  protected $matchConditionDataType = '';
  /**
   * Required. The name for this extension chain. The name is logged as part of
   * the HTTP request logs. The name must conform with RFC-1034, is restricted
   * to lower-cased letters, numbers and hyphens, and can have a maximum length
   * of 63 characters. Additionally, the first character must be a letter and
   * the last a letter or a number.
   *
   * @var string
   */
  public $name;

  /**
   * Required. A set of extensions to execute for the matching request. At least
   * one extension is required. Up to 3 extensions can be defined for each
   * extension chain for `LbTrafficExtension` resource. `LbRouteExtension` and
   * `LbEdgeExtension` chains are limited to 1 extension per extension chain.
   *
   * @param ExtensionChainExtension[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return ExtensionChainExtension[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * Required. Conditions under which this chain is invoked for a request.
   *
   * @param ExtensionChainMatchCondition $matchCondition
   */
  public function setMatchCondition(ExtensionChainMatchCondition $matchCondition)
  {
    $this->matchCondition = $matchCondition;
  }
  /**
   * @return ExtensionChainMatchCondition
   */
  public function getMatchCondition()
  {
    return $this->matchCondition;
  }
  /**
   * Required. The name for this extension chain. The name is logged as part of
   * the HTTP request logs. The name must conform with RFC-1034, is restricted
   * to lower-cased letters, numbers and hyphens, and can have a maximum length
   * of 63 characters. Additionally, the first character must be a letter and
   * the last a letter or a number.
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
class_alias(ExtensionChain::class, 'Google_Service_NetworkServices_ExtensionChain');
