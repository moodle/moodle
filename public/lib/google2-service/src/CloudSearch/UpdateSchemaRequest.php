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

class UpdateSchemaRequest extends \Google\Model
{
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  protected $schemaType = Schema::class;
  protected $schemaDataType = '';
  /**
   * If true, the schema will be checked for validity, but will not be
   * registered with the data source, even if valid.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Common debug options.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * The new schema for the source.
   *
   * @param Schema $schema
   */
  public function setSchema(Schema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return Schema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * If true, the schema will be checked for validity, but will not be
   * registered with the data source, even if valid.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSchemaRequest::class, 'Google_Service_CloudSearch_UpdateSchemaRequest');
