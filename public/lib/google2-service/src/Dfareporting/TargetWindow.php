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

namespace Google\Service\Dfareporting;

class TargetWindow extends \Google\Model
{
  /**
   * Open up a new window to display the backup image Corresponds to "_blank" in
   * html
   */
  public const TARGET_WINDOW_OPTION_NEW_WINDOW = 'NEW_WINDOW';
  /**
   * Use the current window to display the backup image Corresponds to "_top" in
   * html
   */
  public const TARGET_WINDOW_OPTION_CURRENT_WINDOW = 'CURRENT_WINDOW';
  /**
   * User-defined HTML used to display the backup image Corresponds to "other"
   */
  public const TARGET_WINDOW_OPTION_CUSTOM = 'CUSTOM';
  /**
   * User-entered value.
   *
   * @var string
   */
  public $customHtml;
  /**
   * Type of browser window for which the backup image of the flash creative can
   * be displayed.
   *
   * @var string
   */
  public $targetWindowOption;

  /**
   * User-entered value.
   *
   * @param string $customHtml
   */
  public function setCustomHtml($customHtml)
  {
    $this->customHtml = $customHtml;
  }
  /**
   * @return string
   */
  public function getCustomHtml()
  {
    return $this->customHtml;
  }
  /**
   * Type of browser window for which the backup image of the flash creative can
   * be displayed.
   *
   * Accepted values: NEW_WINDOW, CURRENT_WINDOW, CUSTOM
   *
   * @param self::TARGET_WINDOW_OPTION_* $targetWindowOption
   */
  public function setTargetWindowOption($targetWindowOption)
  {
    $this->targetWindowOption = $targetWindowOption;
  }
  /**
   * @return self::TARGET_WINDOW_OPTION_*
   */
  public function getTargetWindowOption()
  {
    return $this->targetWindowOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetWindow::class, 'Google_Service_Dfareporting_TargetWindow');
