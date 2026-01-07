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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2IntentMessageColumnProperties extends \Google\Model
{
  /**
   * Text is aligned to the leading edge of the column.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGNMENT_UNSPECIFIED = 'HORIZONTAL_ALIGNMENT_UNSPECIFIED';
  /**
   * Text is aligned to the leading edge of the column.
   */
  public const HORIZONTAL_ALIGNMENT_LEADING = 'LEADING';
  /**
   * Text is centered in the column.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * Text is aligned to the trailing edge of the column.
   */
  public const HORIZONTAL_ALIGNMENT_TRAILING = 'TRAILING';
  /**
   * Required. Column heading.
   *
   * @var string
   */
  public $header;
  /**
   * Optional. Defines text alignment for all cells in this column.
   *
   * @var string
   */
  public $horizontalAlignment;

  /**
   * Required. Column heading.
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * Optional. Defines text alignment for all cells in this column.
   *
   * Accepted values: HORIZONTAL_ALIGNMENT_UNSPECIFIED, LEADING, CENTER,
   * TRAILING
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2IntentMessageColumnProperties::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2IntentMessageColumnProperties');
