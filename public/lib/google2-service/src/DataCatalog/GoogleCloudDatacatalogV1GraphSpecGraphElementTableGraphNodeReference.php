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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1GraphSpecGraphElementTableGraphNodeReference extends \Google\Collection
{
  protected $collection_key = 'nodeTableColumns';
  /**
   * Required. The referencing columns in the edge table. The size of
   * `edge_table_columns` must be equal to the size of `node_table_columns`.
   *
   * @var string[]
   */
  public $edgeTableColumns;
  /**
   * Required. The reference to the source/destination node of the edge. This
   * name must be a valid `alias` of a node element in the same graph. Example,
   * `Person` node can be a source node name of an edge element
   * `Person_to_Address`.
   *
   * @var string
   */
  public $nodeAlias;
  /**
   * Required. The referenced columns of the source node table.
   *
   * @var string[]
   */
  public $nodeTableColumns;

  /**
   * Required. The referencing columns in the edge table. The size of
   * `edge_table_columns` must be equal to the size of `node_table_columns`.
   *
   * @param string[] $edgeTableColumns
   */
  public function setEdgeTableColumns($edgeTableColumns)
  {
    $this->edgeTableColumns = $edgeTableColumns;
  }
  /**
   * @return string[]
   */
  public function getEdgeTableColumns()
  {
    return $this->edgeTableColumns;
  }
  /**
   * Required. The reference to the source/destination node of the edge. This
   * name must be a valid `alias` of a node element in the same graph. Example,
   * `Person` node can be a source node name of an edge element
   * `Person_to_Address`.
   *
   * @param string $nodeAlias
   */
  public function setNodeAlias($nodeAlias)
  {
    $this->nodeAlias = $nodeAlias;
  }
  /**
   * @return string
   */
  public function getNodeAlias()
  {
    return $this->nodeAlias;
  }
  /**
   * Required. The referenced columns of the source node table.
   *
   * @param string[] $nodeTableColumns
   */
  public function setNodeTableColumns($nodeTableColumns)
  {
    $this->nodeTableColumns = $nodeTableColumns;
  }
  /**
   * @return string[]
   */
  public function getNodeTableColumns()
  {
    return $this->nodeTableColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1GraphSpecGraphElementTableGraphNodeReference::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1GraphSpecGraphElementTableGraphNodeReference');
