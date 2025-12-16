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

class Method extends \Google\Collection
{
  /**
   * Syntax `proto2`.
   */
  public const SYNTAX_SYNTAX_PROTO2 = 'SYNTAX_PROTO2';
  /**
   * Syntax `proto3`.
   */
  public const SYNTAX_SYNTAX_PROTO3 = 'SYNTAX_PROTO3';
  /**
   * Syntax `editions`.
   */
  public const SYNTAX_SYNTAX_EDITIONS = 'SYNTAX_EDITIONS';
  protected $collection_key = 'options';
  /**
   * The source edition string, only valid when syntax is SYNTAX_EDITIONS. This
   * field should be ignored, instead the edition should be inherited from Api.
   * This is similar to Field and EnumValue.
   *
   * @deprecated
   * @var string
   */
  public $edition;
  /**
   * The simple name of this method.
   *
   * @var string
   */
  public $name;
  protected $optionsType = Option::class;
  protected $optionsDataType = 'array';
  /**
   * If true, the request is streamed.
   *
   * @var bool
   */
  public $requestStreaming;
  /**
   * A URL of the input message type.
   *
   * @var string
   */
  public $requestTypeUrl;
  /**
   * If true, the response is streamed.
   *
   * @var bool
   */
  public $responseStreaming;
  /**
   * The URL of the output message type.
   *
   * @var string
   */
  public $responseTypeUrl;
  /**
   * The source syntax of this method. This field should be ignored, instead the
   * syntax should be inherited from Api. This is similar to Field and
   * EnumValue.
   *
   * @deprecated
   * @var string
   */
  public $syntax;

  /**
   * The source edition string, only valid when syntax is SYNTAX_EDITIONS. This
   * field should be ignored, instead the edition should be inherited from Api.
   * This is similar to Field and EnumValue.
   *
   * @deprecated
   * @param string $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * The simple name of this method.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Any metadata attached to the method.
   *
   * @param Option[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return Option[]
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * If true, the request is streamed.
   *
   * @param bool $requestStreaming
   */
  public function setRequestStreaming($requestStreaming)
  {
    $this->requestStreaming = $requestStreaming;
  }
  /**
   * @return bool
   */
  public function getRequestStreaming()
  {
    return $this->requestStreaming;
  }
  /**
   * A URL of the input message type.
   *
   * @param string $requestTypeUrl
   */
  public function setRequestTypeUrl($requestTypeUrl)
  {
    $this->requestTypeUrl = $requestTypeUrl;
  }
  /**
   * @return string
   */
  public function getRequestTypeUrl()
  {
    return $this->requestTypeUrl;
  }
  /**
   * If true, the response is streamed.
   *
   * @param bool $responseStreaming
   */
  public function setResponseStreaming($responseStreaming)
  {
    $this->responseStreaming = $responseStreaming;
  }
  /**
   * @return bool
   */
  public function getResponseStreaming()
  {
    return $this->responseStreaming;
  }
  /**
   * The URL of the output message type.
   *
   * @param string $responseTypeUrl
   */
  public function setResponseTypeUrl($responseTypeUrl)
  {
    $this->responseTypeUrl = $responseTypeUrl;
  }
  /**
   * @return string
   */
  public function getResponseTypeUrl()
  {
    return $this->responseTypeUrl;
  }
  /**
   * The source syntax of this method. This field should be ignored, instead the
   * syntax should be inherited from Api. This is similar to Field and
   * EnumValue.
   *
   * Accepted values: SYNTAX_PROTO2, SYNTAX_PROTO3, SYNTAX_EDITIONS
   *
   * @deprecated
   * @param self::SYNTAX_* $syntax
   */
  public function setSyntax($syntax)
  {
    $this->syntax = $syntax;
  }
  /**
   * @deprecated
   * @return self::SYNTAX_*
   */
  public function getSyntax()
  {
    return $this->syntax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Method::class, 'Google_Service_ServiceNetworking_Method');
