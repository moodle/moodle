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

namespace Google\Service\Bigquery;

class IndexPruningStats extends \Google\Model
{
  protected $baseTableType = TableReference::class;
  protected $baseTableDataType = '';
  /**
   * The index id.
   *
   * @var string
   */
  public $indexId;
  /**
   * The number of parallel inputs after index pruning.
   *
   * @var string
   */
  public $postIndexPruningParallelInputCount;
  /**
   * The number of parallel inputs before index pruning.
   *
   * @var string
   */
  public $preIndexPruningParallelInputCount;

  /**
   * The base table reference.
   *
   * @param TableReference $baseTable
   */
  public function setBaseTable(TableReference $baseTable)
  {
    $this->baseTable = $baseTable;
  }
  /**
   * @return TableReference
   */
  public function getBaseTable()
  {
    return $this->baseTable;
  }
  /**
   * The index id.
   *
   * @param string $indexId
   */
  public function setIndexId($indexId)
  {
    $this->indexId = $indexId;
  }
  /**
   * @return string
   */
  public function getIndexId()
  {
    return $this->indexId;
  }
  /**
   * The number of parallel inputs after index pruning.
   *
   * @param string $postIndexPruningParallelInputCount
   */
  public function setPostIndexPruningParallelInputCount($postIndexPruningParallelInputCount)
  {
    $this->postIndexPruningParallelInputCount = $postIndexPruningParallelInputCount;
  }
  /**
   * @return string
   */
  public function getPostIndexPruningParallelInputCount()
  {
    return $this->postIndexPruningParallelInputCount;
  }
  /**
   * The number of parallel inputs before index pruning.
   *
   * @param string $preIndexPruningParallelInputCount
   */
  public function setPreIndexPruningParallelInputCount($preIndexPruningParallelInputCount)
  {
    $this->preIndexPruningParallelInputCount = $preIndexPruningParallelInputCount;
  }
  /**
   * @return string
   */
  public function getPreIndexPruningParallelInputCount()
  {
    return $this->preIndexPruningParallelInputCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexPruningStats::class, 'Google_Service_Bigquery_IndexPruningStats');
