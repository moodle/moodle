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

namespace Google\Service\ServiceConsumerManagement;

class HttpRule extends \Google\Collection
{
  protected $collection_key = 'additionalBindings';
  protected $additionalBindingsType = HttpRule::class;
  protected $additionalBindingsDataType = 'array';
  /**
   * The name of the request field whose value is mapped to the HTTP request
   * body, or `*` for mapping all request fields not captured by the path
   * pattern to the HTTP body, or omitted for not having any HTTP request body.
   * NOTE: the referred field must be present at the top-level of the request
   * message type.
   *
   * @var string
   */
  public $body;
  protected $customType = CustomHttpPattern::class;
  protected $customDataType = '';
  /**
   * Maps to HTTP DELETE. Used for deleting a resource.
   *
   * @var string
   */
  public $delete;
  /**
   * Maps to HTTP GET. Used for listing and getting information about resources.
   *
   * @var string
   */
  public $get;
  /**
   * Maps to HTTP PATCH. Used for updating a resource.
   *
   * @var string
   */
  public $patch;
  /**
   * Maps to HTTP POST. Used for creating a resource or performing an action.
   *
   * @var string
   */
  public $post;
  /**
   * Maps to HTTP PUT. Used for replacing a resource.
   *
   * @var string
   */
  public $put;
  /**
   * Optional. The name of the response field whose value is mapped to the HTTP
   * response body. When omitted, the entire response message will be used as
   * the HTTP response body. NOTE: The referred field must be present at the
   * top-level of the response message type.
   *
   * @var string
   */
  public $responseBody;
  /**
   * Selects a method to which this rule applies. Refer to selector for syntax
   * details.
   *
   * @var string
   */
  public $selector;

  /**
   * Additional HTTP bindings for the selector. Nested bindings must not contain
   * an `additional_bindings` field themselves (that is, the nesting may only be
   * one level deep).
   *
   * @param HttpRule[] $additionalBindings
   */
  public function setAdditionalBindings($additionalBindings)
  {
    $this->additionalBindings = $additionalBindings;
  }
  /**
   * @return HttpRule[]
   */
  public function getAdditionalBindings()
  {
    return $this->additionalBindings;
  }
  /**
   * The name of the request field whose value is mapped to the HTTP request
   * body, or `*` for mapping all request fields not captured by the path
   * pattern to the HTTP body, or omitted for not having any HTTP request body.
   * NOTE: the referred field must be present at the top-level of the request
   * message type.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * The custom pattern is used for specifying an HTTP method that is not
   * included in the `pattern` field, such as HEAD, or "*" to leave the HTTP
   * method unspecified for this rule. The wild-card rule is useful for services
   * that provide content to Web (HTML) clients.
   *
   * @param CustomHttpPattern $custom
   */
  public function setCustom(CustomHttpPattern $custom)
  {
    $this->custom = $custom;
  }
  /**
   * @return CustomHttpPattern
   */
  public function getCustom()
  {
    return $this->custom;
  }
  /**
   * Maps to HTTP DELETE. Used for deleting a resource.
   *
   * @param string $delete
   */
  public function setDelete($delete)
  {
    $this->delete = $delete;
  }
  /**
   * @return string
   */
  public function getDelete()
  {
    return $this->delete;
  }
  /**
   * Maps to HTTP GET. Used for listing and getting information about resources.
   *
   * @param string $get
   */
  public function setGet($get)
  {
    $this->get = $get;
  }
  /**
   * @return string
   */
  public function getGet()
  {
    return $this->get;
  }
  /**
   * Maps to HTTP PATCH. Used for updating a resource.
   *
   * @param string $patch
   */
  public function setPatch($patch)
  {
    $this->patch = $patch;
  }
  /**
   * @return string
   */
  public function getPatch()
  {
    return $this->patch;
  }
  /**
   * Maps to HTTP POST. Used for creating a resource or performing an action.
   *
   * @param string $post
   */
  public function setPost($post)
  {
    $this->post = $post;
  }
  /**
   * @return string
   */
  public function getPost()
  {
    return $this->post;
  }
  /**
   * Maps to HTTP PUT. Used for replacing a resource.
   *
   * @param string $put
   */
  public function setPut($put)
  {
    $this->put = $put;
  }
  /**
   * @return string
   */
  public function getPut()
  {
    return $this->put;
  }
  /**
   * Optional. The name of the response field whose value is mapped to the HTTP
   * response body. When omitted, the entire response message will be used as
   * the HTTP response body. NOTE: The referred field must be present at the
   * top-level of the response message type.
   *
   * @param string $responseBody
   */
  public function setResponseBody($responseBody)
  {
    $this->responseBody = $responseBody;
  }
  /**
   * @return string
   */
  public function getResponseBody()
  {
    return $this->responseBody;
  }
  /**
   * Selects a method to which this rule applies. Refer to selector for syntax
   * details.
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
class_alias(HttpRule::class, 'Google_Service_ServiceConsumerManagement_HttpRule');
