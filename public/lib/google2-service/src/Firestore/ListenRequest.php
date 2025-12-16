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

namespace Google\Service\Firestore;

class ListenRequest extends \Google\Model
{
  protected $addTargetType = Target::class;
  protected $addTargetDataType = '';
  /**
   * Labels associated with this target change.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The ID of a target to remove from this stream.
   *
   * @var int
   */
  public $removeTarget;

  /**
   * A target to add to this stream.
   *
   * @param Target $addTarget
   */
  public function setAddTarget(Target $addTarget)
  {
    $this->addTarget = $addTarget;
  }
  /**
   * @return Target
   */
  public function getAddTarget()
  {
    return $this->addTarget;
  }
  /**
   * Labels associated with this target change.
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
   * The ID of a target to remove from this stream.
   *
   * @param int $removeTarget
   */
  public function setRemoveTarget($removeTarget)
  {
    $this->removeTarget = $removeTarget;
  }
  /**
   * @return int
   */
  public function getRemoveTarget()
  {
    return $this->removeTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListenRequest::class, 'Google_Service_Firestore_ListenRequest');
