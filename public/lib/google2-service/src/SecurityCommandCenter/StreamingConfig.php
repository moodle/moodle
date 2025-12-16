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

class StreamingConfig extends \Google\Model
{
  /**
   * Expression that defines the filter to apply across create/update events of
   * assets or findings as specified by the event type. The expression is a list
   * of zero or more restrictions combined via logical operators `AND` and `OR`.
   * Parentheses are supported, and `OR` has higher precedence than `AND`.
   * Restrictions have the form ` ` and may have a `-` character in front of
   * them to indicate negation. The fields map to those defined in the
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
   * Expression that defines the filter to apply across create/update events of
   * assets or findings as specified by the event type. The expression is a list
   * of zero or more restrictions combined via logical operators `AND` and `OR`.
   * Parentheses are supported, and `OR` has higher precedence than `AND`.
   * Restrictions have the form ` ` and may have a `-` character in front of
   * them to indicate negation. The fields map to those defined in the
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingConfig::class, 'Google_Service_SecurityCommandCenter_StreamingConfig');
