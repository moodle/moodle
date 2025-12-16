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

namespace Google\Service\TrafficDirectorService;

class ClientConfig extends \Google\Collection
{
  protected $collection_key = 'xdsConfig';
  /**
   * For xDS clients, the scope in which the data is used. For example, gRPC
   * indicates the data plane target or that the data is associated with gRPC
   * server(s).
   *
   * @var string
   */
  public $clientScope;
  protected $genericXdsConfigsType = GenericXdsConfig::class;
  protected $genericXdsConfigsDataType = 'array';
  protected $nodeType = Node::class;
  protected $nodeDataType = '';
  protected $xdsConfigType = PerXdsConfig::class;
  protected $xdsConfigDataType = 'array';

  /**
   * For xDS clients, the scope in which the data is used. For example, gRPC
   * indicates the data plane target or that the data is associated with gRPC
   * server(s).
   *
   * @param string $clientScope
   */
  public function setClientScope($clientScope)
  {
    $this->clientScope = $clientScope;
  }
  /**
   * @return string
   */
  public function getClientScope()
  {
    return $this->clientScope;
  }
  /**
   * Represents generic xDS config and the exact config structure depends on the
   * type URL (like Cluster if it is CDS)
   *
   * @param GenericXdsConfig[] $genericXdsConfigs
   */
  public function setGenericXdsConfigs($genericXdsConfigs)
  {
    $this->genericXdsConfigs = $genericXdsConfigs;
  }
  /**
   * @return GenericXdsConfig[]
   */
  public function getGenericXdsConfigs()
  {
    return $this->genericXdsConfigs;
  }
  /**
   * Node for a particular client.
   *
   * @param Node $node
   */
  public function setNode(Node $node)
  {
    $this->node = $node;
  }
  /**
   * @return Node
   */
  public function getNode()
  {
    return $this->node;
  }
  /**
   * This field is deprecated in favor of generic_xds_configs which is much
   * simpler and uniform in structure.
   *
   * @deprecated
   * @param PerXdsConfig[] $xdsConfig
   */
  public function setXdsConfig($xdsConfig)
  {
    $this->xdsConfig = $xdsConfig;
  }
  /**
   * @deprecated
   * @return PerXdsConfig[]
   */
  public function getXdsConfig()
  {
    return $this->xdsConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClientConfig::class, 'Google_Service_TrafficDirectorService_ClientConfig');
