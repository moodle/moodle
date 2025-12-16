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

class GoogleCloudApihubV1HttpOperation extends \Google\Model
{
  /**
   * Method unspecified.
   */
  public const METHOD_METHOD_UNSPECIFIED = 'METHOD_UNSPECIFIED';
  /**
   * Get Operation type.
   */
  public const METHOD_GET = 'GET';
  /**
   * Put Operation type.
   */
  public const METHOD_PUT = 'PUT';
  /**
   * Post Operation type.
   */
  public const METHOD_POST = 'POST';
  /**
   * Delete Operation type.
   */
  public const METHOD_DELETE = 'DELETE';
  /**
   * Options Operation type.
   */
  public const METHOD_OPTIONS = 'OPTIONS';
  /**
   * Head Operation type.
   */
  public const METHOD_HEAD = 'HEAD';
  /**
   * Patch Operation type.
   */
  public const METHOD_PATCH = 'PATCH';
  /**
   * Trace Operation type.
   */
  public const METHOD_TRACE = 'TRACE';
  /**
   * Optional. Operation method Note: Even though this field is optional, it is
   * required for CreateApiOperation API and we will fail the request if not
   * provided.
   *
   * @var string
   */
  public $method;
  protected $pathType = GoogleCloudApihubV1Path::class;
  protected $pathDataType = '';

  /**
   * Optional. Operation method Note: Even though this field is optional, it is
   * required for CreateApiOperation API and we will fail the request if not
   * provided.
   *
   * Accepted values: METHOD_UNSPECIFIED, GET, PUT, POST, DELETE, OPTIONS, HEAD,
   * PATCH, TRACE
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Optional. The path details for the Operation. Note: Even though this field
   * is optional, it is required for CreateApiOperation API and we will fail the
   * request if not provided.
   *
   * @param GoogleCloudApihubV1Path $path
   */
  public function setPath(GoogleCloudApihubV1Path $path)
  {
    $this->path = $path;
  }
  /**
   * @return GoogleCloudApihubV1Path
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1HttpOperation::class, 'Google_Service_APIhub_GoogleCloudApihubV1HttpOperation');
