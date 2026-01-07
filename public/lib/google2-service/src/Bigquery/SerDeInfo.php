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

class SerDeInfo extends \Google\Model
{
  /**
   * Optional. Name of the SerDe. The maximum length is 256 characters.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Key-value pairs that define the initialization parameters for the
   * serialization library. Maximum size 10 Kib.
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Required. Specifies a fully-qualified class name of the serialization
   * library that is responsible for the translation of data between table
   * representation and the underlying low-level input and output format
   * structures. The maximum length is 256 characters.
   *
   * @var string
   */
  public $serializationLibrary;

  /**
   * Optional. Name of the SerDe. The maximum length is 256 characters.
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
   * Optional. Key-value pairs that define the initialization parameters for the
   * serialization library. Maximum size 10 Kib.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Required. Specifies a fully-qualified class name of the serialization
   * library that is responsible for the translation of data between table
   * representation and the underlying low-level input and output format
   * structures. The maximum length is 256 characters.
   *
   * @param string $serializationLibrary
   */
  public function setSerializationLibrary($serializationLibrary)
  {
    $this->serializationLibrary = $serializationLibrary;
  }
  /**
   * @return string
   */
  public function getSerializationLibrary()
  {
    return $this->serializationLibrary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SerDeInfo::class, 'Google_Service_Bigquery_SerDeInfo');
