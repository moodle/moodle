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

namespace Google\Service\APIManagement;

class HttpOperationHttpResponse extends \Google\Model
{
  protected $headersType = HttpOperationHeader::class;
  protected $headersDataType = 'map';
  /**
   * Map of status code to observed count
   *
   * @var string[]
   */
  public $responseCodes;

  /**
   * Unordered map from header name to header metadata
   *
   * @param HttpOperationHeader[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return HttpOperationHeader[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Map of status code to observed count
   *
   * @param string[] $responseCodes
   */
  public function setResponseCodes($responseCodes)
  {
    $this->responseCodes = $responseCodes;
  }
  /**
   * @return string[]
   */
  public function getResponseCodes()
  {
    return $this->responseCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpOperationHttpResponse::class, 'Google_Service_APIManagement_HttpOperationHttpResponse');
