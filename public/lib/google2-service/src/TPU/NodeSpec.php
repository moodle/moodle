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

namespace Google\Service\TPU;

class NodeSpec extends \Google\Model
{
  protected $multisliceParamsType = MultisliceParams::class;
  protected $multisliceParamsDataType = '';
  protected $nodeType = Node::class;
  protected $nodeDataType = '';
  /**
   * Optional. The unqualified resource name. Should follow the
   * `^[A-Za-z0-9_.~+%-]+$` regex format. This is only specified when requesting
   * a single node. In case of multislice requests, multislice_params must be
   * populated instead.
   *
   * @var string
   */
  public $nodeId;
  /**
   * Required. The parent resource name.
   *
   * @var string
   */
  public $parent;

  /**
   * Optional. Fields to specify in case of multislice request.
   *
   * @param MultisliceParams $multisliceParams
   */
  public function setMultisliceParams(MultisliceParams $multisliceParams)
  {
    $this->multisliceParams = $multisliceParams;
  }
  /**
   * @return MultisliceParams
   */
  public function getMultisliceParams()
  {
    return $this->multisliceParams;
  }
  /**
   * Required. The node.
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
   * Optional. The unqualified resource name. Should follow the
   * `^[A-Za-z0-9_.~+%-]+$` regex format. This is only specified when requesting
   * a single node. In case of multislice requests, multislice_params must be
   * populated instead.
   *
   * @param string $nodeId
   */
  public function setNodeId($nodeId)
  {
    $this->nodeId = $nodeId;
  }
  /**
   * @return string
   */
  public function getNodeId()
  {
    return $this->nodeId;
  }
  /**
   * Required. The parent resource name.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeSpec::class, 'Google_Service_TPU_NodeSpec');
