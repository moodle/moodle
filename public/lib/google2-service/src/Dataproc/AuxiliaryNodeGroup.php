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

namespace Google\Service\Dataproc;

class AuxiliaryNodeGroup extends \Google\Model
{
  protected $nodeGroupType = NodeGroup::class;
  protected $nodeGroupDataType = '';
  /**
   * Optional. A node group ID. Generated if not specified.The ID must contain
   * only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens (-).
   * Cannot begin or end with underscore or hyphen. Must consist of from 3 to 33
   * characters.
   *
   * @var string
   */
  public $nodeGroupId;

  /**
   * Required. Node group configuration.
   *
   * @param NodeGroup $nodeGroup
   */
  public function setNodeGroup(NodeGroup $nodeGroup)
  {
    $this->nodeGroup = $nodeGroup;
  }
  /**
   * @return NodeGroup
   */
  public function getNodeGroup()
  {
    return $this->nodeGroup;
  }
  /**
   * Optional. A node group ID. Generated if not specified.The ID must contain
   * only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens (-).
   * Cannot begin or end with underscore or hyphen. Must consist of from 3 to 33
   * characters.
   *
   * @param string $nodeGroupId
   */
  public function setNodeGroupId($nodeGroupId)
  {
    $this->nodeGroupId = $nodeGroupId;
  }
  /**
   * @return string
   */
  public function getNodeGroupId()
  {
    return $this->nodeGroupId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuxiliaryNodeGroup::class, 'Google_Service_Dataproc_AuxiliaryNodeGroup');
