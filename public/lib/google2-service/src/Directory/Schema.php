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

namespace Google\Service\Directory;

class Schema extends \Google\Collection
{
  protected $collection_key = 'fields';
  /**
   * Display name for the schema.
   *
   * @var string
   */
  public $displayName;
  /**
   * The ETag of the resource.
   *
   * @var string
   */
  public $etag;
  protected $fieldsType = SchemaFieldSpec::class;
  protected $fieldsDataType = 'array';
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;
  /**
   * The unique identifier of the schema (Read-only)
   *
   * @var string
   */
  public $schemaId;
  /**
   * The schema's name. Each `schema_name` must be unique within a customer.
   * Reusing a name results in a `409: Entity already exists` error.
   *
   * @var string
   */
  public $schemaName;

  /**
   * Display name for the schema.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * A list of fields in the schema.
   *
   * @param SchemaFieldSpec[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return SchemaFieldSpec[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Kind of resource this is.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The unique identifier of the schema (Read-only)
   *
   * @param string $schemaId
   */
  public function setSchemaId($schemaId)
  {
    $this->schemaId = $schemaId;
  }
  /**
   * @return string
   */
  public function getSchemaId()
  {
    return $this->schemaId;
  }
  /**
   * The schema's name. Each `schema_name` must be unique within a customer.
   * Reusing a name results in a `409: Entity already exists` error.
   *
   * @param string $schemaName
   */
  public function setSchemaName($schemaName)
  {
    $this->schemaName = $schemaName;
  }
  /**
   * @return string
   */
  public function getSchemaName()
  {
    return $this->schemaName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schema::class, 'Google_Service_Directory_Schema');
