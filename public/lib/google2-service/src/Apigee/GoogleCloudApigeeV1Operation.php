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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Operation extends \Google\Collection
{
  protected $collection_key = 'methods';
  /**
   * methods refers to the REST verbs as in
   * https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html. When none
   * specified, all verb types are allowed.
   *
   * @var string[]
   */
  public $methods;
  /**
   * Required. REST resource path associated with the API proxy or remote
   * service.
   *
   * @var string
   */
  public $resource;

  /**
   * methods refers to the REST verbs as in
   * https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html. When none
   * specified, all verb types are allowed.
   *
   * @param string[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return string[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * Required. REST resource path associated with the API proxy or remote
   * service.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Operation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Operation');
