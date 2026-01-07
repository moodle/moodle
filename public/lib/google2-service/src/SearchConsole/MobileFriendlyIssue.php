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

class MobileFriendlyIssue extends \Google\Model
{
  /**
   * Unknown rule. Sorry, we don't have any description for the rule that was
   * broken.
   */
  public const RULE_MOBILE_FRIENDLY_RULE_UNSPECIFIED = 'MOBILE_FRIENDLY_RULE_UNSPECIFIED';
  /**
   * Plugins incompatible with mobile devices are being used. [Learn more]
   * (https://support.google.com/webmasters/answer/6352293#flash_usage).
   */
  public const RULE_USES_INCOMPATIBLE_PLUGINS = 'USES_INCOMPATIBLE_PLUGINS';
  /**
   * Viewport is not specified using the meta viewport tag. [Learn more] (https:
   * //support.google.com/webmasters/answer/6352293#viewport_not_configured).
   */
  public const RULE_CONFIGURE_VIEWPORT = 'CONFIGURE_VIEWPORT';
  /**
   * Viewport defined to a fixed width. [Learn more]
   * (https://support.google.com/webmasters/answer/6352293#fixed-
   * width_viewport).
   */
  public const RULE_FIXED_WIDTH_VIEWPORT = 'FIXED_WIDTH_VIEWPORT';
  /**
   * Content not sized to viewport. [Learn more] (https://support.google.com/web
   * masters/answer/6352293#content_not_sized_to_viewport).
   */
  public const RULE_SIZE_CONTENT_TO_VIEWPORT = 'SIZE_CONTENT_TO_VIEWPORT';
  /**
   * Font size is too small for easy reading on a small screen. [Learn More]
   * (https://support.google.com/webmasters/answer/6352293#small_font_size).
   */
  public const RULE_USE_LEGIBLE_FONT_SIZES = 'USE_LEGIBLE_FONT_SIZES';
  /**
   * Touch elements are too close to each other. [Learn more] (https://support.g
   * oogle.com/webmasters/answer/6352293#touch_elements_too_close).
   */
  public const RULE_TAP_TARGETS_TOO_CLOSE = 'TAP_TARGETS_TOO_CLOSE';
  /**
   * Rule violated.
   *
   * @var string
   */
  public $rule;

  /**
   * Rule violated.
   *
   * Accepted values: MOBILE_FRIENDLY_RULE_UNSPECIFIED,
   * USES_INCOMPATIBLE_PLUGINS, CONFIGURE_VIEWPORT, FIXED_WIDTH_VIEWPORT,
   * SIZE_CONTENT_TO_VIEWPORT, USE_LEGIBLE_FONT_SIZES, TAP_TARGETS_TOO_CLOSE
   *
   * @param self::RULE_* $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return self::RULE_*
   */
  public function getRule()
  {
    return $this->rule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileFriendlyIssue::class, 'Google_Service_SearchConsole_MobileFriendlyIssue');
