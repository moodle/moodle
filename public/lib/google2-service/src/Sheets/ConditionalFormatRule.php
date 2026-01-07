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

namespace Google\Service\Sheets;

class ConditionalFormatRule extends \Google\Collection
{
  protected $collection_key = 'ranges';
  protected $booleanRuleType = BooleanRule::class;
  protected $booleanRuleDataType = '';
  protected $gradientRuleType = GradientRule::class;
  protected $gradientRuleDataType = '';
  protected $rangesType = GridRange::class;
  protected $rangesDataType = 'array';

  /**
   * The formatting is either "on" or "off" according to the rule.
   *
   * @param BooleanRule $booleanRule
   */
  public function setBooleanRule(BooleanRule $booleanRule)
  {
    $this->booleanRule = $booleanRule;
  }
  /**
   * @return BooleanRule
   */
  public function getBooleanRule()
  {
    return $this->booleanRule;
  }
  /**
   * The formatting will vary based on the gradients in the rule.
   *
   * @param GradientRule $gradientRule
   */
  public function setGradientRule(GradientRule $gradientRule)
  {
    $this->gradientRule = $gradientRule;
  }
  /**
   * @return GradientRule
   */
  public function getGradientRule()
  {
    return $this->gradientRule;
  }
  /**
   * The ranges that are formatted if the condition is true. All the ranges must
   * be on the same grid.
   *
   * @param GridRange[] $ranges
   */
  public function setRanges($ranges)
  {
    $this->ranges = $ranges;
  }
  /**
   * @return GridRange[]
   */
  public function getRanges()
  {
    return $this->ranges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConditionalFormatRule::class, 'Google_Service_Sheets_ConditionalFormatRule');
