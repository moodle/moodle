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

namespace Google\Service\CloudSearch;

class AppsDynamiteStorageDecoratedText extends \Google\Model
{
  /**
   * @var string
   */
  public $bottomLabel;
  protected $buttonType = AppsDynamiteStorageButton::class;
  protected $buttonDataType = '';
  protected $endIconType = AppsDynamiteStorageIcon::class;
  protected $endIconDataType = '';
  protected $iconType = AppsDynamiteStorageIcon::class;
  protected $iconDataType = '';
  protected $onClickType = AppsDynamiteStorageOnClick::class;
  protected $onClickDataType = '';
  protected $startIconType = AppsDynamiteStorageIcon::class;
  protected $startIconDataType = '';
  protected $switchControlType = AppsDynamiteStorageDecoratedTextSwitchControl::class;
  protected $switchControlDataType = '';
  /**
   * @var string
   */
  public $text;
  /**
   * @var string
   */
  public $topLabel;
  /**
   * @var bool
   */
  public $wrapText;

  /**
   * @param string
   */
  public function setBottomLabel($bottomLabel)
  {
    $this->bottomLabel = $bottomLabel;
  }
  /**
   * @return string
   */
  public function getBottomLabel()
  {
    return $this->bottomLabel;
  }
  /**
   * @param AppsDynamiteStorageButton
   */
  public function setButton(AppsDynamiteStorageButton $button)
  {
    $this->button = $button;
  }
  /**
   * @return AppsDynamiteStorageButton
   */
  public function getButton()
  {
    return $this->button;
  }
  /**
   * @param AppsDynamiteStorageIcon
   */
  public function setEndIcon(AppsDynamiteStorageIcon $endIcon)
  {
    $this->endIcon = $endIcon;
  }
  /**
   * @return AppsDynamiteStorageIcon
   */
  public function getEndIcon()
  {
    return $this->endIcon;
  }
  /**
   * @param AppsDynamiteStorageIcon
   */
  public function setIcon(AppsDynamiteStorageIcon $icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return AppsDynamiteStorageIcon
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * @param AppsDynamiteStorageOnClick
   */
  public function setOnClick(AppsDynamiteStorageOnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return AppsDynamiteStorageOnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
  /**
   * @param AppsDynamiteStorageIcon
   */
  public function setStartIcon(AppsDynamiteStorageIcon $startIcon)
  {
    $this->startIcon = $startIcon;
  }
  /**
   * @return AppsDynamiteStorageIcon
   */
  public function getStartIcon()
  {
    return $this->startIcon;
  }
  /**
   * @param AppsDynamiteStorageDecoratedTextSwitchControl
   */
  public function setSwitchControl(AppsDynamiteStorageDecoratedTextSwitchControl $switchControl)
  {
    $this->switchControl = $switchControl;
  }
  /**
   * @return AppsDynamiteStorageDecoratedTextSwitchControl
   */
  public function getSwitchControl()
  {
    return $this->switchControl;
  }
  /**
   * @param string
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * @param string
   */
  public function setTopLabel($topLabel)
  {
    $this->topLabel = $topLabel;
  }
  /**
   * @return string
   */
  public function getTopLabel()
  {
    return $this->topLabel;
  }
  /**
   * @param bool
   */
  public function setWrapText($wrapText)
  {
    $this->wrapText = $wrapText;
  }
  /**
   * @return bool
   */
  public function getWrapText()
  {
    return $this->wrapText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsDynamiteStorageDecoratedText::class, 'Google_Service_CloudSearch_AppsDynamiteStorageDecoratedText');
