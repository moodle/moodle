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

namespace Google\Service\ManagedKafka;

class Context extends \Google\Collection
{
  protected $collection_key = 'subjects';
  /**
   * Identifier. The name of the context. Structured like: `projects/{project}/l
   * ocations/{location}/schemaRegistries/{schema_registry}/contexts/{context}`
   * The context name {context} can contain the following: * Up to 255
   * characters. * Allowed characters: letters (uppercase or lowercase),
   * numbers, and the following special characters: `.`, `-`, `_`, `+`, `%`, and
   * `~`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The subjects of the context.
   *
   * @var string[]
   */
  public $subjects;

  /**
   * Identifier. The name of the context. Structured like: `projects/{project}/l
   * ocations/{location}/schemaRegistries/{schema_registry}/contexts/{context}`
   * The context name {context} can contain the following: * Up to 255
   * characters. * Allowed characters: letters (uppercase or lowercase),
   * numbers, and the following special characters: `.`, `-`, `_`, `+`, `%`, and
   * `~`.
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
   * Optional. The subjects of the context.
   *
   * @param string[] $subjects
   */
  public function setSubjects($subjects)
  {
    $this->subjects = $subjects;
  }
  /**
   * @return string[]
   */
  public function getSubjects()
  {
    return $this->subjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Context::class, 'Google_Service_ManagedKafka_Context');
