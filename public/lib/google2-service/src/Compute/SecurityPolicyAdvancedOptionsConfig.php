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

namespace Google\Service\Compute;

class SecurityPolicyAdvancedOptionsConfig extends \Google\Collection
{
  public const JSON_PARSING_DISABLED = 'DISABLED';
  public const JSON_PARSING_STANDARD = 'STANDARD';
  public const JSON_PARSING_STANDARD_WITH_GRAPHQL = 'STANDARD_WITH_GRAPHQL';
  public const LOG_LEVEL_NORMAL = 'NORMAL';
  public const LOG_LEVEL_VERBOSE = 'VERBOSE';
  protected $collection_key = 'userIpRequestHeaders';
  protected $jsonCustomConfigType = SecurityPolicyAdvancedOptionsConfigJsonCustomConfig::class;
  protected $jsonCustomConfigDataType = '';
  /**
   * @var string
   */
  public $jsonParsing;
  /**
   * @var string
   */
  public $logLevel;
  /**
   * The maximum request size chosen by the customer with Waf enabled. Values
   * supported are "8KB", "16KB, "32KB", "48KB" and "64KB". Values are case
   * insensitive.
   *
   * @var string
   */
  public $requestBodyInspectionSize;
  /**
   * An optional list of case-insensitive request header names to use for
   * resolving the callers client IP address.
   *
   * @var string[]
   */
  public $userIpRequestHeaders;

  /**
   * Custom configuration to apply the JSON parsing. Only applicable when
   * json_parsing is set to STANDARD.
   *
   * @param SecurityPolicyAdvancedOptionsConfigJsonCustomConfig $jsonCustomConfig
   */
  public function setJsonCustomConfig(SecurityPolicyAdvancedOptionsConfigJsonCustomConfig $jsonCustomConfig)
  {
    $this->jsonCustomConfig = $jsonCustomConfig;
  }
  /**
   * @return SecurityPolicyAdvancedOptionsConfigJsonCustomConfig
   */
  public function getJsonCustomConfig()
  {
    return $this->jsonCustomConfig;
  }
  /**
   * @param self::JSON_PARSING_* $jsonParsing
   */
  public function setJsonParsing($jsonParsing)
  {
    $this->jsonParsing = $jsonParsing;
  }
  /**
   * @return self::JSON_PARSING_*
   */
  public function getJsonParsing()
  {
    return $this->jsonParsing;
  }
  /**
   * @param self::LOG_LEVEL_* $logLevel
   */
  public function setLogLevel($logLevel)
  {
    $this->logLevel = $logLevel;
  }
  /**
   * @return self::LOG_LEVEL_*
   */
  public function getLogLevel()
  {
    return $this->logLevel;
  }
  /**
   * The maximum request size chosen by the customer with Waf enabled. Values
   * supported are "8KB", "16KB, "32KB", "48KB" and "64KB". Values are case
   * insensitive.
   *
   * @param string $requestBodyInspectionSize
   */
  public function setRequestBodyInspectionSize($requestBodyInspectionSize)
  {
    $this->requestBodyInspectionSize = $requestBodyInspectionSize;
  }
  /**
   * @return string
   */
  public function getRequestBodyInspectionSize()
  {
    return $this->requestBodyInspectionSize;
  }
  /**
   * An optional list of case-insensitive request header names to use for
   * resolving the callers client IP address.
   *
   * @param string[] $userIpRequestHeaders
   */
  public function setUserIpRequestHeaders($userIpRequestHeaders)
  {
    $this->userIpRequestHeaders = $userIpRequestHeaders;
  }
  /**
   * @return string[]
   */
  public function getUserIpRequestHeaders()
  {
    return $this->userIpRequestHeaders;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyAdvancedOptionsConfig::class, 'Google_Service_Compute_SecurityPolicyAdvancedOptionsConfig');
