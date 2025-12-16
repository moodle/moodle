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

namespace Google\Service\Dataform;

class Assertion extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $dependencyTargetsType = Target::class;
  protected $dependencyTargetsDataType = 'array';
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @var bool
   */
  public $disabled;
  protected $parentActionType = Target::class;
  protected $parentActionDataType = '';
  protected $relationDescriptorType = RelationDescriptor::class;
  protected $relationDescriptorDataType = '';
  /**
   * The SELECT query which must return zero rows in order for this assertion to
   * succeed.
   *
   * @var string
   */
  public $selectQuery;
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @var string[]
   */
  public $tags;

  /**
   * A list of actions that this action depends on.
   *
   * @param Target[] $dependencyTargets
   */
  public function setDependencyTargets($dependencyTargets)
  {
    $this->dependencyTargets = $dependencyTargets;
  }
  /**
   * @return Target[]
   */
  public function getDependencyTargets()
  {
    return $this->dependencyTargets;
  }
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * The parent action of this assertion. Only set if this assertion was
   * automatically generated.
   *
   * @param Target $parentAction
   */
  public function setParentAction(Target $parentAction)
  {
    $this->parentAction = $parentAction;
  }
  /**
   * @return Target
   */
  public function getParentAction()
  {
    return $this->parentAction;
  }
  /**
   * Descriptor for the assertion's automatically-generated view and its
   * columns.
   *
   * @param RelationDescriptor $relationDescriptor
   */
  public function setRelationDescriptor(RelationDescriptor $relationDescriptor)
  {
    $this->relationDescriptor = $relationDescriptor;
  }
  /**
   * @return RelationDescriptor
   */
  public function getRelationDescriptor()
  {
    return $this->relationDescriptor;
  }
  /**
   * The SELECT query which must return zero rows in order for this assertion to
   * succeed.
   *
   * @param string $selectQuery
   */
  public function setSelectQuery($selectQuery)
  {
    $this->selectQuery = $selectQuery;
  }
  /**
   * @return string
   */
  public function getSelectQuery()
  {
    return $this->selectQuery;
  }
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Assertion::class, 'Google_Service_Dataform_Assertion');
