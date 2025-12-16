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

class GradebookSettings extends \Google\Collection
{
  /**
   * No method specified. This is never returned.
   */
  public const CALCULATION_TYPE_CALCULATION_TYPE_UNSPECIFIED = 'CALCULATION_TYPE_UNSPECIFIED';
  /**
   * Overall grade is the sum of grades divided by the sum of total points
   * regardless of category.
   */
  public const CALCULATION_TYPE_TOTAL_POINTS = 'TOTAL_POINTS';
  /**
   * Overall grade is the weighted average by category.
   */
  public const CALCULATION_TYPE_WEIGHTED_CATEGORIES = 'WEIGHTED_CATEGORIES';
  /**
   * No setting specified. This is never returned.
   */
  public const DISPLAY_SETTING_DISPLAY_SETTING_UNSPECIFIED = 'DISPLAY_SETTING_UNSPECIFIED';
  /**
   * Shows overall grade in the gradebook and student profile to both teachers
   * and students.
   */
  public const DISPLAY_SETTING_SHOW_OVERALL_GRADE = 'SHOW_OVERALL_GRADE';
  /**
   * Does not show overall grade in the gradebook or student profile.
   */
  public const DISPLAY_SETTING_HIDE_OVERALL_GRADE = 'HIDE_OVERALL_GRADE';
  /**
   * Shows the overall grade to teachers in the gradebook and student profile.
   * Hides from students in their student profile.
   */
  public const DISPLAY_SETTING_SHOW_TEACHERS_ONLY = 'SHOW_TEACHERS_ONLY';
  protected $collection_key = 'gradeCategories';
  /**
   * Indicates how the overall grade is calculated.
   *
   * @var string
   */
  public $calculationType;
  /**
   * Indicates who can see the overall grade..
   *
   * @var string
   */
  public $displaySetting;
  protected $gradeCategoriesType = GradeCategory::class;
  protected $gradeCategoriesDataType = 'array';

  /**
   * Indicates how the overall grade is calculated.
   *
   * Accepted values: CALCULATION_TYPE_UNSPECIFIED, TOTAL_POINTS,
   * WEIGHTED_CATEGORIES
   *
   * @param self::CALCULATION_TYPE_* $calculationType
   */
  public function setCalculationType($calculationType)
  {
    $this->calculationType = $calculationType;
  }
  /**
   * @return self::CALCULATION_TYPE_*
   */
  public function getCalculationType()
  {
    return $this->calculationType;
  }
  /**
   * Indicates who can see the overall grade..
   *
   * Accepted values: DISPLAY_SETTING_UNSPECIFIED, SHOW_OVERALL_GRADE,
   * HIDE_OVERALL_GRADE, SHOW_TEACHERS_ONLY
   *
   * @param self::DISPLAY_SETTING_* $displaySetting
   */
  public function setDisplaySetting($displaySetting)
  {
    $this->displaySetting = $displaySetting;
  }
  /**
   * @return self::DISPLAY_SETTING_*
   */
  public function getDisplaySetting()
  {
    return $this->displaySetting;
  }
  /**
   * Grade categories that are available for coursework in the course.
   *
   * @param GradeCategory[] $gradeCategories
   */
  public function setGradeCategories($gradeCategories)
  {
    $this->gradeCategories = $gradeCategories;
  }
  /**
   * @return GradeCategory[]
   */
  public function getGradeCategories()
  {
    return $this->gradeCategories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GradebookSettings::class, 'Google_Service_Classroom_GradebookSettings');
