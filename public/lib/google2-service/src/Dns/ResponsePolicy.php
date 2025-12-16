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

namespace Google\Service\Dns;

class ResponsePolicy extends \Google\Collection
{
  protected $collection_key = 'networks';
  /**
   * User-provided description for this Response Policy.
   *
   * @var string
   */
  public $description;
  protected $gkeClustersType = ResponsePolicyGKECluster::class;
  protected $gkeClustersDataType = 'array';
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $kind;
  /**
   * User labels.
   *
   * @var string[]
   */
  public $labels;
  protected $networksType = ResponsePolicyNetwork::class;
  protected $networksDataType = 'array';
  /**
   * User assigned name for this Response Policy.
   *
   * @var string
   */
  public $responsePolicyName;

  /**
   * User-provided description for this Response Policy.
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
   * The list of Google Kubernetes Engine clusters to which this response policy
   * is applied.
   *
   * @param ResponsePolicyGKECluster[] $gkeClusters
   */
  public function setGkeClusters($gkeClusters)
  {
    $this->gkeClusters = $gkeClusters;
  }
  /**
   * @return ResponsePolicyGKECluster[]
   */
  public function getGkeClusters()
  {
    return $this->gkeClusters;
  }
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * User labels.
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
   * List of network names specifying networks to which this policy is applied.
   *
   * @param ResponsePolicyNetwork[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return ResponsePolicyNetwork[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
  /**
   * User assigned name for this Response Policy.
   *
   * @param string $responsePolicyName
   */
  public function setResponsePolicyName($responsePolicyName)
  {
    $this->responsePolicyName = $responsePolicyName;
  }
  /**
   * @return string
   */
  public function getResponsePolicyName()
  {
    return $this->responsePolicyName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponsePolicy::class, 'Google_Service_Dns_ResponsePolicy');
