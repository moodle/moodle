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

namespace Google\Service\SecurityCommandCenter;

class BulkMuteFindingsRequest extends \Google\Model
{
  /**
   * Unused.
   */
  public const MUTE_STATE_MUTE_STATE_UNSPECIFIED = 'MUTE_STATE_UNSPECIFIED';
  /**
   * Matching findings will be muted (default).
   */
  public const MUTE_STATE_MUTED = 'MUTED';
  /**
   * Matching findings will have their mute state cleared.
   */
  public const MUTE_STATE_UNDEFINED = 'UNDEFINED';
  /**
   * Expression that identifies findings that should be updated. The expression
   * is a list of zero or more restrictions combined via logical operators `AND`
   * and `OR`. Parentheses are supported, and `OR` has higher precedence than
   * `AND`. Restrictions have the form ` ` and may have a `-` character in front
   * of them to indicate negation. The fields map to those defined in the
   * corresponding resource. The supported operators are: * `=` for all value
   * types. * `>`, `<`, `>=`, `<=` for integer values. * `:`, meaning substring
   * matching, for strings. The supported value types are: * string literals in
   * quotes. * integer literals without quotes. * boolean literals `true` and
   * `false` without quotes.
   *
   * @var string
   */
  public $filter;
  /**
   * This can be a mute configuration name or any identifier for mute/unmute of
   * findings based on the filter.
   *
   * @deprecated
   * @var string
   */
  public $muteAnnotation;
  /**
   * Optional. All findings matching the given filter will have their mute state
   * set to this value. The default value is `MUTED`. Setting this to
   * `UNDEFINED` will clear the mute state on all matching findings.
   *
   * @var string
   */
  public $muteState;

  /**
   * Expression that identifies findings that should be updated. The expression
   * is a list of zero or more restrictions combined via logical operators `AND`
   * and `OR`. Parentheses are supported, and `OR` has higher precedence than
   * `AND`. Restrictions have the form ` ` and may have a `-` character in front
   * of them to indicate negation. The fields map to those defined in the
   * corresponding resource. The supported operators are: * `=` for all value
   * types. * `>`, `<`, `>=`, `<=` for integer values. * `:`, meaning substring
   * matching, for strings. The supported value types are: * string literals in
   * quotes. * integer literals without quotes. * boolean literals `true` and
   * `false` without quotes.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * This can be a mute configuration name or any identifier for mute/unmute of
   * findings based on the filter.
   *
   * @deprecated
   * @param string $muteAnnotation
   */
  public function setMuteAnnotation($muteAnnotation)
  {
    $this->muteAnnotation = $muteAnnotation;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getMuteAnnotation()
  {
    return $this->muteAnnotation;
  }
  /**
   * Optional. All findings matching the given filter will have their mute state
   * set to this value. The default value is `MUTED`. Setting this to
   * `UNDEFINED` will clear the mute state on all matching findings.
   *
   * Accepted values: MUTE_STATE_UNSPECIFIED, MUTED, UNDEFINED
   *
   * @param self::MUTE_STATE_* $muteState
   */
  public function setMuteState($muteState)
  {
    $this->muteState = $muteState;
  }
  /**
   * @return self::MUTE_STATE_*
   */
  public function getMuteState()
  {
    return $this->muteState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkMuteFindingsRequest::class, 'Google_Service_SecurityCommandCenter_BulkMuteFindingsRequest');
