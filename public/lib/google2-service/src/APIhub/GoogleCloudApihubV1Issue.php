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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Issue extends \Google\Collection
{
  /**
   * Severity unspecified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Severity error.
   */
  public const SEVERITY_SEVERITY_ERROR = 'SEVERITY_ERROR';
  /**
   * Severity warning.
   */
  public const SEVERITY_SEVERITY_WARNING = 'SEVERITY_WARNING';
  /**
   * Severity info.
   */
  public const SEVERITY_SEVERITY_INFO = 'SEVERITY_INFO';
  /**
   * Severity hint.
   */
  public const SEVERITY_SEVERITY_HINT = 'SEVERITY_HINT';
  protected $collection_key = 'path';
  /**
   * Required. Rule code unique to each rule defined in linter.
   *
   * @var string
   */
  public $code;
  /**
   * Required. Human-readable message describing the issue found by the linter.
   *
   * @var string
   */
  public $message;
  /**
   * Required. An array of strings indicating the location in the analyzed
   * document where the rule was triggered.
   *
   * @var string[]
   */
  public $path;
  protected $rangeType = GoogleCloudApihubV1Range::class;
  protected $rangeDataType = '';
  /**
   * Required. Severity level of the rule violation.
   *
   * @var string
   */
  public $severity;

  /**
   * Required. Rule code unique to each rule defined in linter.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Required. Human-readable message describing the issue found by the linter.
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
   * Required. An array of strings indicating the location in the analyzed
   * document where the rule was triggered.
   *
   * @param string[] $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string[]
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Required. Object describing where in the file the issue was found.
   *
   * @param GoogleCloudApihubV1Range $range
   */
  public function setRange(GoogleCloudApihubV1Range $range)
  {
    $this->range = $range;
  }
  /**
   * @return GoogleCloudApihubV1Range
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * Required. Severity level of the rule violation.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, SEVERITY_ERROR, SEVERITY_WARNING,
   * SEVERITY_INFO, SEVERITY_HINT
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
class_alias(GoogleCloudApihubV1Issue::class, 'Google_Service_APIhub_GoogleCloudApihubV1Issue');
