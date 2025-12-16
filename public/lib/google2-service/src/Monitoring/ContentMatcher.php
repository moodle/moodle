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

namespace Google\Service\Monitoring;

class ContentMatcher extends \Google\Model
{
  /**
   * No content matcher type specified (maintained for backward compatibility,
   * but deprecated for future use). Treated as CONTAINS_STRING.
   */
  public const MATCHER_CONTENT_MATCHER_OPTION_UNSPECIFIED = 'CONTENT_MATCHER_OPTION_UNSPECIFIED';
  /**
   * Selects substring matching. The match succeeds if the output contains the
   * content string. This is the default value for checks without a matcher
   * option, or where the value of matcher is
   * CONTENT_MATCHER_OPTION_UNSPECIFIED.
   */
  public const MATCHER_CONTAINS_STRING = 'CONTAINS_STRING';
  /**
   * Selects negation of substring matching. The match succeeds if the output
   * does NOT contain the content string.
   */
  public const MATCHER_NOT_CONTAINS_STRING = 'NOT_CONTAINS_STRING';
  /**
   * Selects regular-expression matching. The match succeeds if the output
   * matches the regular expression specified in the content string. Regex
   * matching is only supported for HTTP/HTTPS checks.
   */
  public const MATCHER_MATCHES_REGEX = 'MATCHES_REGEX';
  /**
   * Selects negation of regular-expression matching. The match succeeds if the
   * output does NOT match the regular expression specified in the content
   * string. Regex matching is only supported for HTTP/HTTPS checks.
   */
  public const MATCHER_NOT_MATCHES_REGEX = 'NOT_MATCHES_REGEX';
  /**
   * Selects JSONPath matching. See JsonPathMatcher for details on when the
   * match succeeds. JSONPath matching is only supported for HTTP/HTTPS checks.
   */
  public const MATCHER_MATCHES_JSON_PATH = 'MATCHES_JSON_PATH';
  /**
   * Selects JSONPath matching. See JsonPathMatcher for details on when the
   * match succeeds. Succeeds when output does NOT match as specified. JSONPath
   * is only supported for HTTP/HTTPS checks.
   */
  public const MATCHER_NOT_MATCHES_JSON_PATH = 'NOT_MATCHES_JSON_PATH';
  /**
   * String, regex or JSON content to match. Maximum 1024 bytes. An empty
   * content string indicates no content matching is to be performed.
   *
   * @var string
   */
  public $content;
  protected $jsonPathMatcherType = JsonPathMatcher::class;
  protected $jsonPathMatcherDataType = '';
  /**
   * The type of content matcher that will be applied to the server output,
   * compared to the content string when the check is run.
   *
   * @var string
   */
  public $matcher;

  /**
   * String, regex or JSON content to match. Maximum 1024 bytes. An empty
   * content string indicates no content matching is to be performed.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Matcher information for MATCHES_JSON_PATH and NOT_MATCHES_JSON_PATH
   *
   * @param JsonPathMatcher $jsonPathMatcher
   */
  public function setJsonPathMatcher(JsonPathMatcher $jsonPathMatcher)
  {
    $this->jsonPathMatcher = $jsonPathMatcher;
  }
  /**
   * @return JsonPathMatcher
   */
  public function getJsonPathMatcher()
  {
    return $this->jsonPathMatcher;
  }
  /**
   * The type of content matcher that will be applied to the server output,
   * compared to the content string when the check is run.
   *
   * Accepted values: CONTENT_MATCHER_OPTION_UNSPECIFIED, CONTAINS_STRING,
   * NOT_CONTAINS_STRING, MATCHES_REGEX, NOT_MATCHES_REGEX, MATCHES_JSON_PATH,
   * NOT_MATCHES_JSON_PATH
   *
   * @param self::MATCHER_* $matcher
   */
  public function setMatcher($matcher)
  {
    $this->matcher = $matcher;
  }
  /**
   * @return self::MATCHER_*
   */
  public function getMatcher()
  {
    return $this->matcher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentMatcher::class, 'Google_Service_Monitoring_ContentMatcher');
