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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1p1beta1NormalizedBoundingBox extends \Google\Model
{
  /**
   * Bottom Y coordinate.
   *
   * @var float
   */
  public $bottom;
  /**
   * Left X coordinate.
   *
   * @var float
   */
  public $left;
  /**
   * Right X coordinate.
   *
   * @var float
   */
  public $right;
  /**
   * Top Y coordinate.
   *
   * @var float
   */
  public $top;

  /**
   * Bottom Y coordinate.
   *
   * @param float $bottom
   */
  public function setBottom($bottom)
  {
    $this->bottom = $bottom;
  }
  /**
   * @return float
   */
  public function getBottom()
  {
    return $this->bottom;
  }
  /**
   * Left X coordinate.
   *
   * @param float $left
   */
  public function setLeft($left)
  {
    $this->left = $left;
  }
  /**
   * @return float
   */
  public function getLeft()
  {
    return $this->left;
  }
  /**
   * Right X coordinate.
   *
   * @param float $right
   */
  public function setRight($right)
  {
    $this->right = $right;
  }
  /**
   * @return float
   */
  public function getRight()
  {
    return $this->right;
  }
  /**
   * Top Y coordinate.
   *
   * @param float $top
   */
  public function setTop($top)
  {
    $this->top = $top;
  }
  /**
   * @return float
   */
  public function getTop()
  {
    return $this->top;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p1beta1NormalizedBoundingBox::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p1beta1NormalizedBoundingBox');
