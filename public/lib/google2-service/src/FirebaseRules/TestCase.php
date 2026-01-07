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

namespace Google\Service\FirebaseRules;

class TestCase extends \Google\Collection
{
  /**
   * Unspecified expectation.
   */
  public const EXPECTATION_EXPECTATION_UNSPECIFIED = 'EXPECTATION_UNSPECIFIED';
  /**
   * Expect an allowed result.
   */
  public const EXPECTATION_ALLOW = 'ALLOW';
  /**
   * Expect a denied result.
   */
  public const EXPECTATION_DENY = 'DENY';
  /**
   * No level has been specified. Defaults to "NONE" behavior.
   */
  public const EXPRESSION_REPORT_LEVEL_LEVEL_UNSPECIFIED = 'LEVEL_UNSPECIFIED';
  /**
   * Do not include any additional information.
   */
  public const EXPRESSION_REPORT_LEVEL_NONE = 'NONE';
  /**
   * Include detailed reporting on expressions evaluated.
   */
  public const EXPRESSION_REPORT_LEVEL_FULL = 'FULL';
  /**
   * Only include the expressions that were visited during evaluation.
   */
  public const EXPRESSION_REPORT_LEVEL_VISITED = 'VISITED';
  /**
   * No encoding has been specified. Defaults to "URL_ENCODED" behavior.
   */
  public const PATH_ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * Treats path segments as URL encoded but with non-encoded separators ("/").
   * This is the default behavior.
   */
  public const PATH_ENCODING_URL_ENCODED = 'URL_ENCODED';
  /**
   * Treats total path as non-URL encoded e.g. raw.
   */
  public const PATH_ENCODING_PLAIN = 'PLAIN';
  protected $collection_key = 'functionMocks';
  /**
   * Test expectation.
   *
   * @var string
   */
  public $expectation;
  /**
   * Specifies what should be included in the response.
   *
   * @var string
   */
  public $expressionReportLevel;
  protected $functionMocksType = FunctionMock::class;
  protected $functionMocksDataType = 'array';
  /**
   * Specifies whether paths (such as request.path) are encoded and how.
   *
   * @var string
   */
  public $pathEncoding;
  /**
   * Request context. The exact format of the request context is service-
   * dependent. See the appropriate service documentation for information about
   * the supported fields and types on the request. Minimally, all services
   * support the following fields and types: Request field | Type
   * ---------------|----------------- auth.uid | `string` auth.token | `map`
   * headers | `map` method | `string` params | `map` path | `string` time |
   * `google.protobuf.Timestamp` If the request value is not well-formed for the
   * service, the request will be rejected as an invalid argument.
   *
   * @var array
   */
  public $request;
  /**
   * Optional resource value as it appears in persistent storage before the
   * request is fulfilled. The resource type depends on the `request.path`
   * value.
   *
   * @var array
   */
  public $resource;

  /**
   * Test expectation.
   *
   * Accepted values: EXPECTATION_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::EXPECTATION_* $expectation
   */
  public function setExpectation($expectation)
  {
    $this->expectation = $expectation;
  }
  /**
   * @return self::EXPECTATION_*
   */
  public function getExpectation()
  {
    return $this->expectation;
  }
  /**
   * Specifies what should be included in the response.
   *
   * Accepted values: LEVEL_UNSPECIFIED, NONE, FULL, VISITED
   *
   * @param self::EXPRESSION_REPORT_LEVEL_* $expressionReportLevel
   */
  public function setExpressionReportLevel($expressionReportLevel)
  {
    $this->expressionReportLevel = $expressionReportLevel;
  }
  /**
   * @return self::EXPRESSION_REPORT_LEVEL_*
   */
  public function getExpressionReportLevel()
  {
    return $this->expressionReportLevel;
  }
  /**
   * Optional function mocks for service-defined functions. If not set, any
   * service defined function is expected to return an error, which may or may
   * not influence the test outcome.
   *
   * @param FunctionMock[] $functionMocks
   */
  public function setFunctionMocks($functionMocks)
  {
    $this->functionMocks = $functionMocks;
  }
  /**
   * @return FunctionMock[]
   */
  public function getFunctionMocks()
  {
    return $this->functionMocks;
  }
  /**
   * Specifies whether paths (such as request.path) are encoded and how.
   *
   * Accepted values: ENCODING_UNSPECIFIED, URL_ENCODED, PLAIN
   *
   * @param self::PATH_ENCODING_* $pathEncoding
   */
  public function setPathEncoding($pathEncoding)
  {
    $this->pathEncoding = $pathEncoding;
  }
  /**
   * @return self::PATH_ENCODING_*
   */
  public function getPathEncoding()
  {
    return $this->pathEncoding;
  }
  /**
   * Request context. The exact format of the request context is service-
   * dependent. See the appropriate service documentation for information about
   * the supported fields and types on the request. Minimally, all services
   * support the following fields and types: Request field | Type
   * ---------------|----------------- auth.uid | `string` auth.token | `map`
   * headers | `map` method | `string` params | `map` path | `string` time |
   * `google.protobuf.Timestamp` If the request value is not well-formed for the
   * service, the request will be rejected as an invalid argument.
   *
   * @param array $request
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }
  /**
   * @return array
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Optional resource value as it appears in persistent storage before the
   * request is fulfilled. The resource type depends on the `request.path`
   * value.
   *
   * @param array $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return array
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestCase::class, 'Google_Service_FirebaseRules_TestCase');
