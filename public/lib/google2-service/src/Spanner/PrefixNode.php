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

class PrefixNode extends \Google\Model
{
  /**
   * Whether this corresponds to a data_source name.
   *
   * @var bool
   */
  public $dataSourceNode;
  /**
   * The depth in the prefix hierarchy.
   *
   * @var int
   */
  public $depth;
  /**
   * The index of the end key bucket of the range that this node spans.
   *
   * @var int
   */
  public $endIndex;
  /**
   * The index of the start key bucket of the range that this node spans.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The string represented by the prefix node.
   *
   * @var string
   */
  public $word;

  /**
   * Whether this corresponds to a data_source name.
   *
   * @param bool $dataSourceNode
   */
  public function setDataSourceNode($dataSourceNode)
  {
    $this->dataSourceNode = $dataSourceNode;
  }
  /**
   * @return bool
   */
  public function getDataSourceNode()
  {
    return $this->dataSourceNode;
  }
  /**
   * The depth in the prefix hierarchy.
   *
   * @param int $depth
   */
  public function setDepth($depth)
  {
    $this->depth = $depth;
  }
  /**
   * @return int
   */
  public function getDepth()
  {
    return $this->depth;
  }
  /**
   * The index of the end key bucket of the range that this node spans.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * The index of the start key bucket of the range that this node spans.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The string represented by the prefix node.
   *
   * @param string $word
   */
  public function setWord($word)
  {
    $this->word = $word;
  }
  /**
   * @return string
   */
  public function getWord()
  {
    return $this->word;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrefixNode::class, 'Google_Service_Spanner_PrefixNode');
