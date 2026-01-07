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

namespace Google\Service\RecommendationsAI;

class GoogleApiHttpBody extends \Google\Collection
{
  protected $collection_key = 'extensions';
  /**
   * The HTTP Content-Type header value specifying the content type of the body.
   *
   * @var string
   */
  public $contentType;
  /**
   * The HTTP request/response body as raw binary.
   *
   * @var string
   */
  public $data;
  /**
   * Application specific response metadata. Must be set in the first response
   * for streaming APIs.
   *
   * @var array[]
   */
  public $extensions;

  /**
   * The HTTP Content-Type header value specifying the content type of the body.
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * The HTTP request/response body as raw binary.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Application specific response metadata. Must be set in the first response
   * for streaming APIs.
   *
   * @param array[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return array[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiHttpBody::class, 'Google_Service_RecommendationsAI_GoogleApiHttpBody');
