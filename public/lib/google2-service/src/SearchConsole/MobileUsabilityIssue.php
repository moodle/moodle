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

namespace Google\Service\SearchConsole;

class MobileUsabilityIssue extends \Google\Model
{
  /**
   * Unknown issue. Sorry, we don't have any description for the rule that was
   * broken.
   */
  public const ISSUE_TYPE_MOBILE_USABILITY_ISSUE_TYPE_UNSPECIFIED = 'MOBILE_USABILITY_ISSUE_TYPE_UNSPECIFIED';
  /**
   * Plugins incompatible with mobile devices are being used. [Learn more]
   * (https://support.google.com/webmasters/answer/6352293#flash_usage#error-
   * list).
   */
  public const ISSUE_TYPE_USES_INCOMPATIBLE_PLUGINS = 'USES_INCOMPATIBLE_PLUGINS';
  /**
   * Viewport is not specified using the meta viewport tag. [Learn more] (https:
   * //support.google.com/webmasters/answer/6352293#viewport_not_configured#erro
   * r-list).
   */
  public const ISSUE_TYPE_CONFIGURE_VIEWPORT = 'CONFIGURE_VIEWPORT';
  /**
   * Viewport defined to a fixed width. [Learn more]
   * (https://support.google.com/webmasters/answer/6352293#fixed-
   * width_viewport#error-list).
   */
  public const ISSUE_TYPE_FIXED_WIDTH_VIEWPORT = 'FIXED_WIDTH_VIEWPORT';
  /**
   * Content not sized to viewport. [Learn more] (https://support.google.com/web
   * masters/answer/6352293#content_not_sized_to_viewport#error-list).
   */
  public const ISSUE_TYPE_SIZE_CONTENT_TO_VIEWPORT = 'SIZE_CONTENT_TO_VIEWPORT';
  /**
   * Font size is too small for easy reading on a small screen. [Learn More] (ht
   * tps://support.google.com/webmasters/answer/6352293#small_font_size#error-
   * list).
   */
  public const ISSUE_TYPE_USE_LEGIBLE_FONT_SIZES = 'USE_LEGIBLE_FONT_SIZES';
  /**
   * Touch elements are too close to each other. [Learn more] (https://support.g
   * oogle.com/webmasters/answer/6352293#touch_elements_too_close#error-list).
   */
  public const ISSUE_TYPE_TAP_TARGETS_TOO_CLOSE = 'TAP_TARGETS_TOO_CLOSE';
  /**
   * Unknown severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Warning.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Error.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Mobile-usability issue type.
   *
   * @var string
   */
  public $issueType;
  /**
   * Additional information regarding the issue.
   *
   * @var string
   */
  public $message;
  /**
   * Not returned; reserved for future use.
   *
   * @var string
   */
  public $severity;

  /**
   * Mobile-usability issue type.
   *
   * Accepted values: MOBILE_USABILITY_ISSUE_TYPE_UNSPECIFIED,
   * USES_INCOMPATIBLE_PLUGINS, CONFIGURE_VIEWPORT, FIXED_WIDTH_VIEWPORT,
   * SIZE_CONTENT_TO_VIEWPORT, USE_LEGIBLE_FONT_SIZES, TAP_TARGETS_TOO_CLOSE
   *
   * @param self::ISSUE_TYPE_* $issueType
   */
  public function setIssueType($issueType)
  {
    $this->issueType = $issueType;
  }
  /**
   * @return self::ISSUE_TYPE_*
   */
  public function getIssueType()
  {
    return $this->issueType;
  }
  /**
   * Additional information regarding the issue.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Not returned; reserved for future use.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, WARNING, ERROR
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileUsabilityIssue::class, 'Google_Service_SearchConsole_MobileUsabilityIssue');
