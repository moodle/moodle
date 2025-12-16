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

class NodeGroup extends \Google\Collection
{
  protected $collection_key = 'roles';
  /**
   * Optional. Node group labels. Label keys must consist of from 1 to 63
   * characters and conform to RFC 1035 (https://www.ietf.org/rfc/rfc1035.txt).
   * Label values can be empty. If specified, they must consist of from 1 to 63
   * characters and conform to RFC 1035 (https://www.ietf.org/rfc/rfc1035.txt).
   * The node group must have no more than 32 labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The Node group resource name (https://aip.dev/122).
   *
   * @var string
   */
  public $name;
  protected $nodeGroupConfigType = InstanceGroupConfig::class;
  protected $nodeGroupConfigDataType = '';
  /**
   * Required. Node group roles.
   *
   * @var string[]
   */
  public $roles;

  /**
   * Optional. Node group labels. Label keys must consist of from 1 to 63
   * characters and conform to RFC 1035 (https://www.ietf.org/rfc/rfc1035.txt).
   * Label values can be empty. If specified, they must consist of from 1 to 63
   * characters and conform to RFC 1035 (https://www.ietf.org/rfc/rfc1035.txt).
   * The node group must have no more than 32 labels.
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
   * The Node group resource name (https://aip.dev/122).
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
   * Optional. The node group instance group configuration.
   *
   * @param InstanceGroupConfig $nodeGroupConfig
   */
  public function setNodeGroupConfig(InstanceGroupConfig $nodeGroupConfig)
  {
    $this->nodeGroupConfig = $nodeGroupConfig;
  }
  /**
   * @return InstanceGroupConfig
   */
  public function getNodeGroupConfig()
  {
    return $this->nodeGroupConfig;
  }
  /**
   * Required. Node group roles.
   *
   * @param string[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return string[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroup::class, 'Google_Service_Dataproc_NodeGroup');
