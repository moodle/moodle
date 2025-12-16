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

namespace Google\Service\Bigquery;

class UserDefinedFunctionResource extends \Google\Model
{
  /**
   * [Pick one] An inline resource that contains code for a user-defined
   * function (UDF). Providing a inline code resource is equivalent to providing
   * a URI for a file containing the same code.
   *
   * @var string
   */
  public $inlineCode;
  /**
   * [Pick one] A code resource to load from a Google Cloud Storage URI
   * (gs://bucket/path).
   *
   * @var string
   */
  public $resourceUri;

  /**
   * [Pick one] An inline resource that contains code for a user-defined
   * function (UDF). Providing a inline code resource is equivalent to providing
   * a URI for a file containing the same code.
   *
   * @param string $inlineCode
   */
  public function setInlineCode($inlineCode)
  {
    $this->inlineCode = $inlineCode;
  }
  /**
   * @return string
   */
  public function getInlineCode()
  {
    return $this->inlineCode;
  }
  /**
   * [Pick one] A code resource to load from a Google Cloud Storage URI
   * (gs://bucket/path).
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserDefinedFunctionResource::class, 'Google_Service_Bigquery_UserDefinedFunctionResource');
