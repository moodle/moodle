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

namespace Google\Service\CloudSearch;

class ObjectDefinition extends \Google\Collection
{
  protected $collection_key = 'propertyDefinitions';
  /**
   * The name for the object, which then defines its type. Item indexing
   * requests should set the objectType field equal to this value. For example,
   * if *name* is *Document*, then indexing requests for items of type Document
   * should set objectType equal to *Document*. Each object definition must be
   * uniquely named within a schema. The name must start with a letter and can
   * only contain letters (A-Z, a-z) or numbers (0-9). The maximum length is 256
   * characters.
   *
   * @var string
   */
  public $name;
  protected $optionsType = ObjectOptions::class;
  protected $optionsDataType = '';
  protected $propertyDefinitionsType = PropertyDefinition::class;
  protected $propertyDefinitionsDataType = 'array';

  /**
   * The name for the object, which then defines its type. Item indexing
   * requests should set the objectType field equal to this value. For example,
   * if *name* is *Document*, then indexing requests for items of type Document
   * should set objectType equal to *Document*. Each object definition must be
   * uniquely named within a schema. The name must start with a letter and can
   * only contain letters (A-Z, a-z) or numbers (0-9). The maximum length is 256
   * characters.
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
   * The optional object-specific options.
   *
   * @param ObjectOptions $options
   */
  public function setOptions(ObjectOptions $options)
  {
    $this->options = $options;
  }
  /**
   * @return ObjectOptions
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * The property definitions for the object. The maximum number of elements is
   * 1000.
   *
   * @param PropertyDefinition[] $propertyDefinitions
   */
  public function setPropertyDefinitions($propertyDefinitions)
  {
    $this->propertyDefinitions = $propertyDefinitions;
  }
  /**
   * @return PropertyDefinition[]
   */
  public function getPropertyDefinitions()
  {
    return $this->propertyDefinitions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectDefinition::class, 'Google_Service_CloudSearch_ObjectDefinition');
