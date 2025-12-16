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

namespace Google\Service\NetworkServices;

class GrpcRouteMethodMatch extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Will only match the exact name provided.
   */
  public const TYPE_EXACT = 'EXACT';
  /**
   * Will interpret grpc_method and grpc_service as regexes. RE2 syntax is
   * supported.
   */
  public const TYPE_REGULAR_EXPRESSION = 'REGULAR_EXPRESSION';
  /**
   * Optional. Specifies that matches are case sensitive. The default value is
   * true. case_sensitive must not be used with a type of REGULAR_EXPRESSION.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * Required. Name of the method to match against. If unspecified, will match
   * all methods.
   *
   * @var string
   */
  public $grpcMethod;
  /**
   * Required. Name of the service to match against. If unspecified, will match
   * all services.
   *
   * @var string
   */
  public $grpcService;
  /**
   * Optional. Specifies how to match against the name. If not specified, a
   * default value of "EXACT" is used.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Specifies that matches are case sensitive. The default value is
   * true. case_sensitive must not be used with a type of REGULAR_EXPRESSION.
   *
   * @param bool $caseSensitive
   */
  public function setCaseSensitive($caseSensitive)
  {
    $this->caseSensitive = $caseSensitive;
  }
  /**
   * @return bool
   */
  public function getCaseSensitive()
  {
    return $this->caseSensitive;
  }
  /**
   * Required. Name of the method to match against. If unspecified, will match
   * all methods.
   *
   * @param string $grpcMethod
   */
  public function setGrpcMethod($grpcMethod)
  {
    $this->grpcMethod = $grpcMethod;
  }
  /**
   * @return string
   */
  public function getGrpcMethod()
  {
    return $this->grpcMethod;
  }
  /**
   * Required. Name of the service to match against. If unspecified, will match
   * all services.
   *
   * @param string $grpcService
   */
  public function setGrpcService($grpcService)
  {
    $this->grpcService = $grpcService;
  }
  /**
   * @return string
   */
  public function getGrpcService()
  {
    return $this->grpcService;
  }
  /**
   * Optional. Specifies how to match against the name. If not specified, a
   * default value of "EXACT" is used.
   *
   * Accepted values: TYPE_UNSPECIFIED, EXACT, REGULAR_EXPRESSION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GrpcRouteMethodMatch::class, 'Google_Service_NetworkServices_GrpcRouteMethodMatch');
