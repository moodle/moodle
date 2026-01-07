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

class HttpHeaderAction extends \Google\Collection
{
  protected $collection_key = 'responseHeadersToRemove';
  protected $requestHeadersToAddType = HttpHeaderOption::class;
  protected $requestHeadersToAddDataType = 'array';
  /**
   * A list of header names for headers that need to be removed from the request
   * before forwarding the request to the backendService.
   *
   * @var string[]
   */
  public $requestHeadersToRemove;
  protected $responseHeadersToAddType = HttpHeaderOption::class;
  protected $responseHeadersToAddDataType = 'array';
  /**
   * A list of header names for headers that need to be removed from the
   * response before sending the response back to the client.
   *
   * @var string[]
   */
  public $responseHeadersToRemove;

  /**
   * Headers to add to a matching request before forwarding the request to
   * thebackendService.
   *
   * @param HttpHeaderOption[] $requestHeadersToAdd
   */
  public function setRequestHeadersToAdd($requestHeadersToAdd)
  {
    $this->requestHeadersToAdd = $requestHeadersToAdd;
  }
  /**
   * @return HttpHeaderOption[]
   */
  public function getRequestHeadersToAdd()
  {
    return $this->requestHeadersToAdd;
  }
  /**
   * A list of header names for headers that need to be removed from the request
   * before forwarding the request to the backendService.
   *
   * @param string[] $requestHeadersToRemove
   */
  public function setRequestHeadersToRemove($requestHeadersToRemove)
  {
    $this->requestHeadersToRemove = $requestHeadersToRemove;
  }
  /**
   * @return string[]
   */
  public function getRequestHeadersToRemove()
  {
    return $this->requestHeadersToRemove;
  }
  /**
   * Headers to add the response before sending the response back to the client.
   *
   * @param HttpHeaderOption[] $responseHeadersToAdd
   */
  public function setResponseHeadersToAdd($responseHeadersToAdd)
  {
    $this->responseHeadersToAdd = $responseHeadersToAdd;
  }
  /**
   * @return HttpHeaderOption[]
   */
  public function getResponseHeadersToAdd()
  {
    return $this->responseHeadersToAdd;
  }
  /**
   * A list of header names for headers that need to be removed from the
   * response before sending the response back to the client.
   *
   * @param string[] $responseHeadersToRemove
   */
  public function setResponseHeadersToRemove($responseHeadersToRemove)
  {
    $this->responseHeadersToRemove = $responseHeadersToRemove;
  }
  /**
   * @return string[]
   */
  public function getResponseHeadersToRemove()
  {
    return $this->responseHeadersToRemove;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpHeaderAction::class, 'Google_Service_Compute_HttpHeaderAction');
