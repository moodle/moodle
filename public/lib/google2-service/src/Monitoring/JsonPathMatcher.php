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

class JsonPathMatcher extends \Google\Model
{
  /**
   * No JSONPath matcher type specified (not valid).
   */
  public const JSON_MATCHER_JSON_PATH_MATCHER_OPTION_UNSPECIFIED = 'JSON_PATH_MATCHER_OPTION_UNSPECIFIED';
  /**
   * Selects 'exact string' matching. The match succeeds if the content at the
   * json_path within the output is exactly the same as the content string.
   */
  public const JSON_MATCHER_EXACT_MATCH = 'EXACT_MATCH';
  /**
   * Selects regular-expression matching. The match succeeds if the content at
   * the json_path within the output matches the regular expression specified in
   * the content string.
   */
  public const JSON_MATCHER_REGEX_MATCH = 'REGEX_MATCH';
  /**
   * The type of JSONPath match that will be applied to the JSON output
   * (ContentMatcher.content)
   *
   * @var string
   */
  public $jsonMatcher;
  /**
   * JSONPath within the response output pointing to the expected
   * ContentMatcher::content to match against.
   *
   * @var string
   */
  public $jsonPath;

  /**
   * The type of JSONPath match that will be applied to the JSON output
   * (ContentMatcher.content)
   *
   * Accepted values: JSON_PATH_MATCHER_OPTION_UNSPECIFIED, EXACT_MATCH,
   * REGEX_MATCH
   *
   * @param self::JSON_MATCHER_* $jsonMatcher
   */
  public function setJsonMatcher($jsonMatcher)
  {
    $this->jsonMatcher = $jsonMatcher;
  }
  /**
   * @return self::JSON_MATCHER_*
   */
  public function getJsonMatcher()
  {
    return $this->jsonMatcher;
  }
  /**
   * JSONPath within the response output pointing to the expected
   * ContentMatcher::content to match against.
   *
   * @param string $jsonPath
   */
  public function setJsonPath($jsonPath)
  {
    $this->jsonPath = $jsonPath;
  }
  /**
   * @return string
   */
  public function getJsonPath()
  {
    return $this->jsonPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JsonPathMatcher::class, 'Google_Service_Monitoring_JsonPathMatcher');
