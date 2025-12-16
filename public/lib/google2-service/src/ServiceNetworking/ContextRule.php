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

namespace Google\Service\ServiceNetworking;

class ContextRule extends \Google\Collection
{
  protected $collection_key = 'requested';
  /**
   * A list of full type names or extension IDs of extensions allowed in grpc
   * side channel from client to backend.
   *
   * @var string[]
   */
  public $allowedRequestExtensions;
  /**
   * A list of full type names or extension IDs of extensions allowed in grpc
   * side channel from backend to client.
   *
   * @var string[]
   */
  public $allowedResponseExtensions;
  /**
   * A list of full type names of provided contexts. It is used to support
   * propagating HTTP headers and ETags from the response extension.
   *
   * @var string[]
   */
  public $provided;
  /**
   * A list of full type names of requested contexts, only the requested context
   * will be made available to the backend.
   *
   * @var string[]
   */
  public $requested;
  /**
   * Selects the methods to which this rule applies. Refer to selector for
   * syntax details.
   *
   * @var string
   */
  public $selector;

  /**
   * A list of full type names or extension IDs of extensions allowed in grpc
   * side channel from client to backend.
   *
   * @param string[] $allowedRequestExtensions
   */
  public function setAllowedRequestExtensions($allowedRequestExtensions)
  {
    $this->allowedRequestExtensions = $allowedRequestExtensions;
  }
  /**
   * @return string[]
   */
  public function getAllowedRequestExtensions()
  {
    return $this->allowedRequestExtensions;
  }
  /**
   * A list of full type names or extension IDs of extensions allowed in grpc
   * side channel from backend to client.
   *
   * @param string[] $allowedResponseExtensions
   */
  public function setAllowedResponseExtensions($allowedResponseExtensions)
  {
    $this->allowedResponseExtensions = $allowedResponseExtensions;
  }
  /**
   * @return string[]
   */
  public function getAllowedResponseExtensions()
  {
    return $this->allowedResponseExtensions;
  }
  /**
   * A list of full type names of provided contexts. It is used to support
   * propagating HTTP headers and ETags from the response extension.
   *
   * @param string[] $provided
   */
  public function setProvided($provided)
  {
    $this->provided = $provided;
  }
  /**
   * @return string[]
   */
  public function getProvided()
  {
    return $this->provided;
  }
  /**
   * A list of full type names of requested contexts, only the requested context
   * will be made available to the backend.
   *
   * @param string[] $requested
   */
  public function setRequested($requested)
  {
    $this->requested = $requested;
  }
  /**
   * @return string[]
   */
  public function getRequested()
  {
    return $this->requested;
  }
  /**
   * Selects the methods to which this rule applies. Refer to selector for
   * syntax details.
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContextRule::class, 'Google_Service_ServiceNetworking_ContextRule');
