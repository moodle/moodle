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

namespace Google\Service\Aiplatform;

class CloudAiLargeModelsVisionNamedBoundingBox extends \Google\Collection
{
  protected $collection_key = 'scores';
  /**
   * @var string[]
   */
  public $classes;
  /**
   * @var string[]
   */
  public $entities;
  /**
   * @var float[]
   */
  public $scores;
  /**
   * @var float
   */
  public $x1;
  /**
   * @var float
   */
  public $x2;
  /**
   * @var float
   */
  public $y1;
  /**
   * @var float
   */
  public $y2;

  /**
   * @param string[] $classes
   */
  public function setClasses($classes)
  {
    $this->classes = $classes;
  }
  /**
   * @return string[]
   */
  public function getClasses()
  {
    return $this->classes;
  }
  /**
   * @param string[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return string[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * @param float[] $scores
   */
  public function setScores($scores)
  {
    $this->scores = $scores;
  }
  /**
   * @return float[]
   */
  public function getScores()
  {
    return $this->scores;
  }
  /**
   * @param float $x1
   */
  public function setX1($x1)
  {
    $this->x1 = $x1;
  }
  /**
   * @return float
   */
  public function getX1()
  {
    return $this->x1;
  }
  /**
   * @param float $x2
   */
  public function setX2($x2)
  {
    $this->x2 = $x2;
  }
  /**
   * @return float
   */
  public function getX2()
  {
    return $this->x2;
  }
  /**
   * @param float $y1
   */
  public function setY1($y1)
  {
    $this->y1 = $y1;
  }
  /**
   * @return float
   */
  public function getY1()
  {
    return $this->y1;
  }
  /**
   * @param float $y2
   */
  public function setY2($y2)
  {
    $this->y2 = $y2;
  }
  /**
   * @return float
   */
  public function getY2()
  {
    return $this->y2;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionNamedBoundingBox::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionNamedBoundingBox');
