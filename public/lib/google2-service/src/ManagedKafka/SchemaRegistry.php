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

class SchemaRegistry extends \Google\Collection
{
  protected $collection_key = 'contexts';
  /**
   * Output only. The contexts of the schema registry instance.
   *
   * @var string[]
   */
  public $contexts;
  /**
   * Identifier. The name of the schema registry instance. Structured like: `pro
   * jects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * The instance name {schema_registry} can contain the following: * Up to 255
   * characters. * Letters (uppercase or lowercase), numbers, and underscores.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The contexts of the schema registry instance.
   *
   * @param string[] $contexts
   */
  public function setContexts($contexts)
  {
    $this->contexts = $contexts;
  }
  /**
   * @return string[]
   */
  public function getContexts()
  {
    return $this->contexts;
  }
  /**
   * Identifier. The name of the schema registry instance. Structured like: `pro
   * jects/{project}/locations/{location}/schemaRegistries/{schema_registry}`
   * The instance name {schema_registry} can contain the following: * Up to 255
   * characters. * Letters (uppercase or lowercase), numbers, and underscores.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaRegistry::class, 'Google_Service_ManagedKafka_SchemaRegistry');
