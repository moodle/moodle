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

namespace Google\Service\AdMob;

class ReportRowDimensionValue extends \Google\Model
{
  /**
   * The localized string representation of the value. If unspecified, the
   * display label should be derived from the value.
   *
   * @var string
   */
  public $displayLabel;
  /**
   * Dimension value in the format specified in the report's spec Dimension
   * enum.
   *
   * @var string
   */
  public $value;

  /**
   * The localized string representation of the value. If unspecified, the
   * display label should be derived from the value.
   *
   * @param string $displayLabel
   */
  public function setDisplayLabel($displayLabel)
  {
    $this->displayLabel = $displayLabel;
  }
  /**
   * @return string
   */
  public function getDisplayLabel()
  {
    return $this->displayLabel;
  }
  /**
   * Dimension value in the format specified in the report's spec Dimension
   * enum.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRowDimensionValue::class, 'Google_Service_AdMob_ReportRowDimensionValue');
