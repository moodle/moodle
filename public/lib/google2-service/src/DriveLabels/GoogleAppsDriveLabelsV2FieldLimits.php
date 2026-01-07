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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2FieldLimits extends \Google\Model
{
  protected $dateLimitsType = GoogleAppsDriveLabelsV2DateLimits::class;
  protected $dateLimitsDataType = '';
  protected $integerLimitsType = GoogleAppsDriveLabelsV2IntegerLimits::class;
  protected $integerLimitsDataType = '';
  protected $longTextLimitsType = GoogleAppsDriveLabelsV2LongTextLimits::class;
  protected $longTextLimitsDataType = '';
  /**
   * Limits for field description, also called help text.
   *
   * @var int
   */
  public $maxDescriptionLength;
  /**
   * Limits for field title.
   *
   * @var int
   */
  public $maxDisplayNameLength;
  /**
   * Maximum length for the id.
   *
   * @var int
   */
  public $maxIdLength;
  protected $selectionLimitsType = GoogleAppsDriveLabelsV2SelectionLimits::class;
  protected $selectionLimitsDataType = '';
  protected $textLimitsType = GoogleAppsDriveLabelsV2TextLimits::class;
  protected $textLimitsDataType = '';
  protected $userLimitsType = GoogleAppsDriveLabelsV2UserLimits::class;
  protected $userLimitsDataType = '';

  /**
   * Date field limits.
   *
   * @param GoogleAppsDriveLabelsV2DateLimits $dateLimits
   */
  public function setDateLimits(GoogleAppsDriveLabelsV2DateLimits $dateLimits)
  {
    $this->dateLimits = $dateLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DateLimits
   */
  public function getDateLimits()
  {
    return $this->dateLimits;
  }
  /**
   * Integer field limits.
   *
   * @param GoogleAppsDriveLabelsV2IntegerLimits $integerLimits
   */
  public function setIntegerLimits(GoogleAppsDriveLabelsV2IntegerLimits $integerLimits)
  {
    $this->integerLimits = $integerLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2IntegerLimits
   */
  public function getIntegerLimits()
  {
    return $this->integerLimits;
  }
  /**
   * Long text field limits.
   *
   * @param GoogleAppsDriveLabelsV2LongTextLimits $longTextLimits
   */
  public function setLongTextLimits(GoogleAppsDriveLabelsV2LongTextLimits $longTextLimits)
  {
    $this->longTextLimits = $longTextLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LongTextLimits
   */
  public function getLongTextLimits()
  {
    return $this->longTextLimits;
  }
  /**
   * Limits for field description, also called help text.
   *
   * @param int $maxDescriptionLength
   */
  public function setMaxDescriptionLength($maxDescriptionLength)
  {
    $this->maxDescriptionLength = $maxDescriptionLength;
  }
  /**
   * @return int
   */
  public function getMaxDescriptionLength()
  {
    return $this->maxDescriptionLength;
  }
  /**
   * Limits for field title.
   *
   * @param int $maxDisplayNameLength
   */
  public function setMaxDisplayNameLength($maxDisplayNameLength)
  {
    $this->maxDisplayNameLength = $maxDisplayNameLength;
  }
  /**
   * @return int
   */
  public function getMaxDisplayNameLength()
  {
    return $this->maxDisplayNameLength;
  }
  /**
   * Maximum length for the id.
   *
   * @param int $maxIdLength
   */
  public function setMaxIdLength($maxIdLength)
  {
    $this->maxIdLength = $maxIdLength;
  }
  /**
   * @return int
   */
  public function getMaxIdLength()
  {
    return $this->maxIdLength;
  }
  /**
   * Selection field limits.
   *
   * @param GoogleAppsDriveLabelsV2SelectionLimits $selectionLimits
   */
  public function setSelectionLimits(GoogleAppsDriveLabelsV2SelectionLimits $selectionLimits)
  {
    $this->selectionLimits = $selectionLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2SelectionLimits
   */
  public function getSelectionLimits()
  {
    return $this->selectionLimits;
  }
  /**
   * The relevant limits for the specified Field.Type. Text field limits.
   *
   * @param GoogleAppsDriveLabelsV2TextLimits $textLimits
   */
  public function setTextLimits(GoogleAppsDriveLabelsV2TextLimits $textLimits)
  {
    $this->textLimits = $textLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2TextLimits
   */
  public function getTextLimits()
  {
    return $this->textLimits;
  }
  /**
   * User field limits.
   *
   * @param GoogleAppsDriveLabelsV2UserLimits $userLimits
   */
  public function setUserLimits(GoogleAppsDriveLabelsV2UserLimits $userLimits)
  {
    $this->userLimits = $userLimits;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserLimits
   */
  public function getUserLimits()
  {
    return $this->userLimits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldLimits::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldLimits');
