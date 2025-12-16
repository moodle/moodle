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

namespace Google\Service\Dataflow;

class BoundedTrieNode extends \Google\Model
{
  protected $childrenType = BoundedTrieNode::class;
  protected $childrenDataType = 'map';
  /**
   * Whether this node has been truncated. A truncated leaf represents possibly
   * many children with the same prefix.
   *
   * @var bool
   */
  public $truncated;

  /**
   * Children of this node. Must be empty if truncated is true.
   *
   * @param BoundedTrieNode[] $children
   */
  public function setChildren($children)
  {
    $this->children = $children;
  }
  /**
   * @return BoundedTrieNode[]
   */
  public function getChildren()
  {
    return $this->children;
  }
  /**
   * Whether this node has been truncated. A truncated leaf represents possibly
   * many children with the same prefix.
   *
   * @param bool $truncated
   */
  public function setTruncated($truncated)
  {
    $this->truncated = $truncated;
  }
  /**
   * @return bool
   */
  public function getTruncated()
  {
    return $this->truncated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BoundedTrieNode::class, 'Google_Service_Dataflow_BoundedTrieNode');
