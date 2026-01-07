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

class MultisliceParams extends \Google\Model
{
  /**
   * Required. Number of nodes with this spec. The system will attempt to
   * provision "node_count" nodes as part of the request. This needs to be > 1.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * Optional. Prefix of node_ids in case of multislice request. Should follow
   * the `^[A-Za-z0-9_.~+%-]+$` regex format. If node_count = 3 and
   * node_id_prefix = "np", node ids of nodes created will be "np-0", "np-1",
   * "np-2". If this field is not provided we use queued_resource_id as the
   * node_id_prefix.
   *
   * @var string
   */
  public $nodeIdPrefix;

  /**
   * Required. Number of nodes with this spec. The system will attempt to
   * provision "node_count" nodes as part of the request. This needs to be > 1.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Optional. Prefix of node_ids in case of multislice request. Should follow
   * the `^[A-Za-z0-9_.~+%-]+$` regex format. If node_count = 3 and
   * node_id_prefix = "np", node ids of nodes created will be "np-0", "np-1",
   * "np-2". If this field is not provided we use queued_resource_id as the
   * node_id_prefix.
   *
   * @param string $nodeIdPrefix
   */
  public function setNodeIdPrefix($nodeIdPrefix)
  {
    $this->nodeIdPrefix = $nodeIdPrefix;
  }
  /**
   * @return string
   */
  public function getNodeIdPrefix()
  {
    return $this->nodeIdPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultisliceParams::class, 'Google_Service_TPU_MultisliceParams');
