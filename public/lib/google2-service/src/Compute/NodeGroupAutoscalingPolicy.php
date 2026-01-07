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

namespace Google\Service\Compute;

class NodeGroupAutoscalingPolicy extends \Google\Model
{
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Autoscaling is disabled.
   */
  public const MODE_OFF = 'OFF';
  /**
   * Autocaling is fully enabled.
   */
  public const MODE_ON = 'ON';
  /**
   * Autoscaling will only scale out and will not remove nodes.
   */
  public const MODE_ONLY_SCALE_OUT = 'ONLY_SCALE_OUT';
  /**
   * The maximum number of nodes that the group should have. Must be set if
   * autoscaling is enabled. Maximum value allowed is 100.
   *
   * @var int
   */
  public $maxNodes;
  /**
   * The minimum number of nodes that the group should have.
   *
   * @var int
   */
  public $minNodes;
  /**
   * The autoscaling mode. Set to one of: ON, OFF, or ONLY_SCALE_OUT. For more
   * information, see  Autoscaler modes.
   *
   * @var string
   */
  public $mode;

  /**
   * The maximum number of nodes that the group should have. Must be set if
   * autoscaling is enabled. Maximum value allowed is 100.
   *
   * @param int $maxNodes
   */
  public function setMaxNodes($maxNodes)
  {
    $this->maxNodes = $maxNodes;
  }
  /**
   * @return int
   */
  public function getMaxNodes()
  {
    return $this->maxNodes;
  }
  /**
   * The minimum number of nodes that the group should have.
   *
   * @param int $minNodes
   */
  public function setMinNodes($minNodes)
  {
    $this->minNodes = $minNodes;
  }
  /**
   * @return int
   */
  public function getMinNodes()
  {
    return $this->minNodes;
  }
  /**
   * The autoscaling mode. Set to one of: ON, OFF, or ONLY_SCALE_OUT. For more
   * information, see  Autoscaler modes.
   *
   * Accepted values: MODE_UNSPECIFIED, OFF, ON, ONLY_SCALE_OUT
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroupAutoscalingPolicy::class, 'Google_Service_Compute_NodeGroupAutoscalingPolicy');
