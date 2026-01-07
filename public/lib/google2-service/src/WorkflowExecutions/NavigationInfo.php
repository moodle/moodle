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

namespace Google\Service\WorkflowExecutions;

class NavigationInfo extends \Google\Collection
{
  protected $collection_key = 'children';
  /**
   * Step entries that can be reached by "stepping into" e.g. a subworkflow
   * call.
   *
   * @var string[]
   */
  public $children;
  /**
   * The index of the next step in the current workflow, if any.
   *
   * @var string
   */
  public $next;
  /**
   * The step entry, if any, that can be reached by "stepping out" of the
   * current workflow being executed.
   *
   * @var string
   */
  public $parent;
  /**
   * The index of the previous step in the current workflow, if any.
   *
   * @var string
   */
  public $previous;

  /**
   * Step entries that can be reached by "stepping into" e.g. a subworkflow
   * call.
   *
   * @param string[] $children
   */
  public function setChildren($children)
  {
    $this->children = $children;
  }
  /**
   * @return string[]
   */
  public function getChildren()
  {
    return $this->children;
  }
  /**
   * The index of the next step in the current workflow, if any.
   *
   * @param string $next
   */
  public function setNext($next)
  {
    $this->next = $next;
  }
  /**
   * @return string
   */
  public function getNext()
  {
    return $this->next;
  }
  /**
   * The step entry, if any, that can be reached by "stepping out" of the
   * current workflow being executed.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The index of the previous step in the current workflow, if any.
   *
   * @param string $previous
   */
  public function setPrevious($previous)
  {
    $this->previous = $previous;
  }
  /**
   * @return string
   */
  public function getPrevious()
  {
    return $this->previous;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NavigationInfo::class, 'Google_Service_WorkflowExecutions_NavigationInfo');
