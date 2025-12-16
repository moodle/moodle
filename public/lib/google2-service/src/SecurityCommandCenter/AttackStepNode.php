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

namespace Google\Service\SecurityCommandCenter;

class AttackStepNode extends \Google\Model
{
  /**
   * Type not specified
   */
  public const TYPE_NODE_TYPE_UNSPECIFIED = 'NODE_TYPE_UNSPECIFIED';
  /**
   * Incoming edge joined with AND
   */
  public const TYPE_NODE_TYPE_AND = 'NODE_TYPE_AND';
  /**
   * Incoming edge joined with OR
   */
  public const TYPE_NODE_TYPE_OR = 'NODE_TYPE_OR';
  /**
   * Incoming edge is defense
   */
  public const TYPE_NODE_TYPE_DEFENSE = 'NODE_TYPE_DEFENSE';
  /**
   * Incoming edge is attacker
   */
  public const TYPE_NODE_TYPE_ATTACKER = 'NODE_TYPE_ATTACKER';
  /**
   * Attack step description
   *
   * @var string
   */
  public $description;
  /**
   * User friendly name of the attack step
   *
   * @var string
   */
  public $displayName;
  /**
   * Attack step labels for metadata
   *
   * @var string[]
   */
  public $labels;
  /**
   * Attack step type. Can be either AND, OR or DEFENSE
   *
   * @var string
   */
  public $type;
  /**
   * Unique ID for one Node
   *
   * @var string
   */
  public $uuid;

  /**
   * Attack step description
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
   * User friendly name of the attack step
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Attack step labels for metadata
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
   * Attack step type. Can be either AND, OR or DEFENSE
   *
   * Accepted values: NODE_TYPE_UNSPECIFIED, NODE_TYPE_AND, NODE_TYPE_OR,
   * NODE_TYPE_DEFENSE, NODE_TYPE_ATTACKER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Unique ID for one Node
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttackStepNode::class, 'Google_Service_SecurityCommandCenter_AttackStepNode');
