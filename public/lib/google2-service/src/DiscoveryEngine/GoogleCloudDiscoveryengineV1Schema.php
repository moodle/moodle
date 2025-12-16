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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1Schema extends \Google\Model
{
  /**
   * The JSON representation of the schema.
   *
   * @var string
   */
  public $jsonSchema;
  /**
   * Immutable. The full resource name of the schema, in the format of `projects
   * /{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/schemas/{schema}`. This field must be a UTF-8 encoded string with a
   * length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  /**
   * The structured representation of the schema.
   *
   * @var array[]
   */
  public $structSchema;

  /**
   * The JSON representation of the schema.
   *
   * @param string $jsonSchema
   */
  public function setJsonSchema($jsonSchema)
  {
    $this->jsonSchema = $jsonSchema;
  }
  /**
   * @return string
   */
  public function getJsonSchema()
  {
    return $this->jsonSchema;
  }
  /**
   * Immutable. The full resource name of the schema, in the format of `projects
   * /{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore}/schemas/{schema}`. This field must be a UTF-8 encoded string with a
   * length limit of 1024 characters.
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
   * The structured representation of the schema.
   *
   * @param array[] $structSchema
   */
  public function setStructSchema($structSchema)
  {
    $this->structSchema = $structSchema;
  }
  /**
   * @return array[]
   */
  public function getStructSchema()
  {
    return $this->structSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Schema::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Schema');
