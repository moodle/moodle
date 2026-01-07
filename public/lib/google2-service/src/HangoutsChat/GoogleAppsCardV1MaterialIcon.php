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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1MaterialIcon extends \Google\Model
{
  /**
   * Whether the icon renders as filled. Default value is false. To preview
   * different icon settings, go to [Google Font
   * Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
   *
   * @var bool
   */
  public $fill;
  /**
   * Weight and grade affect a symbol’s thickness. Adjustments to grade are more
   * granular than adjustments to weight and have a small impact on the size of
   * the symbol. Choose from {-25, 0, 200}. If absent, default value is 0. If
   * any other value is specified, the default value is used. To preview
   * different icon settings, go to [Google Font
   * Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
   *
   * @var int
   */
  public $grade;
  /**
   * The icon name defined in the [Google Material
   * Icon](https://fonts.google.com/icons), for example, `check_box`. Any
   * invalid names are abandoned and replaced with empty string and results in
   * the icon failing to render.
   *
   * @var string
   */
  public $name;
  /**
   * The stroke weight of the icon. Choose from {100, 200, 300, 400, 500, 600,
   * 700}. If absent, default value is 400. If any other value is specified, the
   * default value is used. To preview different icon settings, go to [Google
   * Font Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
   *
   * @var int
   */
  public $weight;

  /**
   * Whether the icon renders as filled. Default value is false. To preview
   * different icon settings, go to [Google Font
   * Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
   *
   * @param bool $fill
   */
  public function setFill($fill)
  {
    $this->fill = $fill;
  }
  /**
   * @return bool
   */
  public function getFill()
  {
    return $this->fill;
  }
  /**
   * Weight and grade affect a symbol’s thickness. Adjustments to grade are more
   * granular than adjustments to weight and have a small impact on the size of
   * the symbol. Choose from {-25, 0, 200}. If absent, default value is 0. If
   * any other value is specified, the default value is used. To preview
   * different icon settings, go to [Google Font
   * Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
   *
   * @param int $grade
   */
  public function setGrade($grade)
  {
    $this->grade = $grade;
  }
  /**
   * @return int
   */
  public function getGrade()
  {
    return $this->grade;
  }
  /**
   * The icon name defined in the [Google Material
   * Icon](https://fonts.google.com/icons), for example, `check_box`. Any
   * invalid names are abandoned and replaced with empty string and results in
   * the icon failing to render.
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
   * The stroke weight of the icon. Choose from {100, 200, 300, 400, 500, 600,
   * 700}. If absent, default value is 400. If any other value is specified, the
   * default value is used. To preview different icon settings, go to [Google
   * Font Icons](https://fonts.google.com/icons) and adjust the settings under
   * **Customize**.
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
class_alias(GoogleAppsCardV1MaterialIcon::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1MaterialIcon');
