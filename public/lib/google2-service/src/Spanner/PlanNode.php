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

namespace Google\Service\Spanner;

class PlanNode extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const KIND_KIND_UNSPECIFIED = 'KIND_UNSPECIFIED';
  /**
   * Denotes a Relational operator node in the expression tree. Relational
   * operators represent iterative processing of rows during query execution.
   * For example, a `TableScan` operation that reads rows from a table.
   */
  public const KIND_RELATIONAL = 'RELATIONAL';
  /**
   * Denotes a Scalar node in the expression tree. Scalar nodes represent non-
   * iterable entities in the query plan. For example, constants or arithmetic
   * operators appearing inside predicate expressions or references to column
   * names.
   */
  public const KIND_SCALAR = 'SCALAR';
  protected $collection_key = 'childLinks';
  protected $childLinksType = ChildLink::class;
  protected $childLinksDataType = 'array';
  /**
   * The display name for the node.
   *
   * @var string
   */
  public $displayName;
  /**
   * The execution statistics associated with the node, contained in a group of
   * key-value pairs. Only present if the plan was returned as a result of a
   * profile query. For example, number of executions, number of rows/time per
   * execution etc.
   *
   * @var array[]
   */
  public $executionStats;
  /**
   * The `PlanNode`'s index in node list.
   *
   * @var int
   */
  public $index;
  /**
   * Used to determine the type of node. May be needed for visualizing different
   * kinds of nodes differently. For example, If the node is a SCALAR node, it
   * will have a condensed representation which can be used to directly embed a
   * description of the node in its parent.
   *
   * @var string
   */
  public $kind;
  /**
   * Attributes relevant to the node contained in a group of key-value pairs.
   * For example, a Parameter Reference node could have the following
   * information in its metadata: { "parameter_reference": "param1",
   * "parameter_type": "array" }
   *
   * @var array[]
   */
  public $metadata;
  protected $shortRepresentationType = ShortRepresentation::class;
  protected $shortRepresentationDataType = '';

  /**
   * List of child node `index`es and their relationship to this parent.
   *
   * @param ChildLink[] $childLinks
   */
  public function setChildLinks($childLinks)
  {
    $this->childLinks = $childLinks;
  }
  /**
   * @return ChildLink[]
   */
  public function getChildLinks()
  {
    return $this->childLinks;
  }
  /**
   * The display name for the node.
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
   * The execution statistics associated with the node, contained in a group of
   * key-value pairs. Only present if the plan was returned as a result of a
   * profile query. For example, number of executions, number of rows/time per
   * execution etc.
   *
   * @param array[] $executionStats
   */
  public function setExecutionStats($executionStats)
  {
    $this->executionStats = $executionStats;
  }
  /**
   * @return array[]
   */
  public function getExecutionStats()
  {
    return $this->executionStats;
  }
  /**
   * The `PlanNode`'s index in node list.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Used to determine the type of node. May be needed for visualizing different
   * kinds of nodes differently. For example, If the node is a SCALAR node, it
   * will have a condensed representation which can be used to directly embed a
   * description of the node in its parent.
   *
   * Accepted values: KIND_UNSPECIFIED, RELATIONAL, SCALAR
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Attributes relevant to the node contained in a group of key-value pairs.
   * For example, a Parameter Reference node could have the following
   * information in its metadata: { "parameter_reference": "param1",
   * "parameter_type": "array" }
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Condensed representation for SCALAR nodes.
   *
   * @param ShortRepresentation $shortRepresentation
   */
  public function setShortRepresentation(ShortRepresentation $shortRepresentation)
  {
    $this->shortRepresentation = $shortRepresentation;
  }
  /**
   * @return ShortRepresentation
   */
  public function getShortRepresentation()
  {
    return $this->shortRepresentation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlanNode::class, 'Google_Service_Spanner_PlanNode');
