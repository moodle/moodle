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

namespace Google\Service\BlockchainNodeEngine;

class PolygonDetails extends \Google\Model
{
  protected $additionalEndpointsType = PolygonEndpoints::class;
  protected $additionalEndpointsDataType = '';
  /**
   * @var string
   */
  public $blockProducerClient;
  /**
   * @var string
   */
  public $network;
  /**
   * @var string
   */
  public $nodeType;
  /**
   * @var string
   */
  public $validationClient;

  /**
   * @param PolygonEndpoints
   */
  public function setAdditionalEndpoints(PolygonEndpoints $additionalEndpoints)
  {
    $this->additionalEndpoints = $additionalEndpoints;
  }
  /**
   * @return PolygonEndpoints
   */
  public function getAdditionalEndpoints()
  {
    return $this->additionalEndpoints;
  }
  /**
   * @param string
   */
  public function setBlockProducerClient($blockProducerClient)
  {
    $this->blockProducerClient = $blockProducerClient;
  }
  /**
   * @return string
   */
  public function getBlockProducerClient()
  {
    return $this->blockProducerClient;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return string
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
  /**
   * @param string
   */
  public function setValidationClient($validationClient)
  {
    $this->validationClient = $validationClient;
  }
  /**
   * @return string
   */
  public function getValidationClient()
  {
    return $this->validationClient;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolygonDetails::class, 'Google_Service_BlockchainNodeEngine_PolygonDetails');
