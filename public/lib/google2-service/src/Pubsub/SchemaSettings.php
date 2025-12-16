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

namespace Google\Service\Pubsub;

class SchemaSettings extends \Google\Model
{
  /**
   * Unspecified
   */
  public const ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * JSON encoding
   */
  public const ENCODING_JSON = 'JSON';
  /**
   * Binary encoding, as defined by the schema type. For some schema types,
   * binary encoding may not be available.
   */
  public const ENCODING_BINARY = 'BINARY';
  /**
   * Optional. The encoding of messages validated against `schema`.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. The minimum (inclusive) revision allowed for validating messages.
   * If empty or not present, allow any revision to be validated against
   * last_revision or any revision created before.
   *
   * @var string
   */
  public $firstRevisionId;
  /**
   * Optional. The maximum (inclusive) revision allowed for validating messages.
   * If empty or not present, allow any revision to be validated against
   * first_revision or any revision created after.
   *
   * @var string
   */
  public $lastRevisionId;
  /**
   * Required. The name of the schema that messages published should be
   * validated against. Format is `projects/{project}/schemas/{schema}`. The
   * value of this field will be `_deleted-schema_` if the schema has been
   * deleted.
   *
   * @var string
   */
  public $schema;

  /**
   * Optional. The encoding of messages validated against `schema`.
   *
   * Accepted values: ENCODING_UNSPECIFIED, JSON, BINARY
   *
   * @param self::ENCODING_* $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return self::ENCODING_*
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Optional. The minimum (inclusive) revision allowed for validating messages.
   * If empty or not present, allow any revision to be validated against
   * last_revision or any revision created before.
   *
   * @param string $firstRevisionId
   */
  public function setFirstRevisionId($firstRevisionId)
  {
    $this->firstRevisionId = $firstRevisionId;
  }
  /**
   * @return string
   */
  public function getFirstRevisionId()
  {
    return $this->firstRevisionId;
  }
  /**
   * Optional. The maximum (inclusive) revision allowed for validating messages.
   * If empty or not present, allow any revision to be validated against
   * first_revision or any revision created after.
   *
   * @param string $lastRevisionId
   */
  public function setLastRevisionId($lastRevisionId)
  {
    $this->lastRevisionId = $lastRevisionId;
  }
  /**
   * @return string
   */
  public function getLastRevisionId()
  {
    return $this->lastRevisionId;
  }
  /**
   * Required. The name of the schema that messages published should be
   * validated against. Format is `projects/{project}/schemas/{schema}`. The
   * value of this field will be `_deleted-schema_` if the schema has been
   * deleted.
   *
   * @param string $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaSettings::class, 'Google_Service_Pubsub_SchemaSettings');
