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

class ChartAxisViewWindowOptions extends \Google\Model
{
  /**
   * The default view window mode used in the Sheets editor for this chart type.
   * In most cases, if set, the default mode is equivalent to `PRETTY`.
   */
  public const VIEW_WINDOW_MODE_DEFAULT_VIEW_WINDOW_MODE = 'DEFAULT_VIEW_WINDOW_MODE';
  /**
   * Do not use. Represents that the currently set mode is not supported by the
   * API.
   */
  public const VIEW_WINDOW_MODE_VIEW_WINDOW_MODE_UNSUPPORTED = 'VIEW_WINDOW_MODE_UNSUPPORTED';
  /**
   * Follows the min and max exactly if specified. If a value is unspecified, it
   * will fall back to the `PRETTY` value.
   */
  public const VIEW_WINDOW_MODE_EXPLICIT = 'EXPLICIT';
  /**
   * Chooses a min and max that make the chart look good. Both min and max are
   * ignored in this mode.
   */
  public const VIEW_WINDOW_MODE_PRETTY = 'PRETTY';
  /**
   * The maximum numeric value to be shown in this view window. If unset, will
   * automatically determine a maximum value that looks good for the data.
   *
   * @var 
   */
  public $viewWindowMax;
  /**
   * The minimum numeric value to be shown in this view window. If unset, will
   * automatically determine a minimum value that looks good for the data.
   *
   * @var 
   */
  public $viewWindowMin;
  /**
   * The view window's mode.
   *
   * @var string
   */
  public $viewWindowMode;

  public function setViewWindowMax($viewWindowMax)
  {
    $this->viewWindowMax = $viewWindowMax;
  }
  public function getViewWindowMax()
  {
    return $this->viewWindowMax;
  }
  public function setViewWindowMin($viewWindowMin)
  {
    $this->viewWindowMin = $viewWindowMin;
  }
  public function getViewWindowMin()
  {
    return $this->viewWindowMin;
  }
  /**
   * The view window's mode.
   *
   * Accepted values: DEFAULT_VIEW_WINDOW_MODE, VIEW_WINDOW_MODE_UNSUPPORTED,
   * EXPLICIT, PRETTY
   *
   * @param self::VIEW_WINDOW_MODE_* $viewWindowMode
   */
  public function setViewWindowMode($viewWindowMode)
  {
    $this->viewWindowMode = $viewWindowMode;
  }
  /**
   * @return self::VIEW_WINDOW_MODE_*
   */
  public function getViewWindowMode()
  {
    return $this->viewWindowMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChartAxisViewWindowOptions::class, 'Google_Service_Sheets_ChartAxisViewWindowOptions');
