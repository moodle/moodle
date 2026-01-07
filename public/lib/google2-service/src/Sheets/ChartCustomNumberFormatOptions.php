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

class ChartCustomNumberFormatOptions extends \Google\Model
{
  /**
   * Custom prefix to be prepended to the chart attribute. This field is
   * optional.
   *
   * @var string
   */
  public $prefix;
  /**
   * Custom suffix to be appended to the chart attribute. This field is
   * optional.
   *
   * @var string
   */
  public $suffix;

  /**
   * Custom prefix to be prepended to the chart attribute. This field is
   * optional.
   *
   * @param string $prefix
   */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }
  /**
   * @return string
   */
  public function getPrefix()
  {
    return $this->prefix;
  }
  /**
   * Custom suffix to be appended to the chart attribute. This field is
   * optional.
   *
   * @param string $suffix
   */
  public function setSuffix($suffix)
  {
    $this->suffix = $suffix;
  }
  /**
   * @return string
   */
  public function getSuffix()
  {
    return $this->suffix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChartCustomNumberFormatOptions::class, 'Google_Service_Sheets_ChartCustomNumberFormatOptions');
