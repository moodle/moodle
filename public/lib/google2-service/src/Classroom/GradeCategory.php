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

namespace Google\Service\Classroom;

class GradeCategory extends \Google\Model
{
  /**
   * Default value of denominator. Only applicable when grade calculation type
   * is TOTAL_POINTS.
   *
   * @var int
   */
  public $defaultGradeDenominator;
  /**
   * ID of the grade category.
   *
   * @var string
   */
  public $id;
  /**
   * Name of the grade category.
   *
   * @var string
   */
  public $name;
  /**
   * The weight of the category average as part of overall average. A weight of
   * 12.34% is represented as 123400 (100% is 1,000,000). The last two digits
   * should always be zero since we use two decimal precision. Only applicable
   * when grade calculation type is WEIGHTED_CATEGORIES.
   *
   * @var int
   */
  public $weight;

  /**
   * Default value of denominator. Only applicable when grade calculation type
   * is TOTAL_POINTS.
   *
   * @param int $defaultGradeDenominator
   */
  public function setDefaultGradeDenominator($defaultGradeDenominator)
  {
    $this->defaultGradeDenominator = $defaultGradeDenominator;
  }
  /**
   * @return int
   */
  public function getDefaultGradeDenominator()
  {
    return $this->defaultGradeDenominator;
  }
  /**
   * ID of the grade category.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Name of the grade category.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The weight of the category average as part of overall average. A weight of
   * 12.34% is represented as 123400 (100% is 1,000,000). The last two digits
   * should always be zero since we use two decimal precision. Only applicable
   * when grade calculation type is WEIGHTED_CATEGORIES.
   *
   * @param int $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return int
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GradeCategory::class, 'Google_Service_Classroom_GradeCategory');
