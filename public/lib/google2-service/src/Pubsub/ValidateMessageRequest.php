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

class ValidateMessageRequest extends \Google\Model
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
   * The encoding expected for messages
   *
   * @var string
   */
  public $encoding;
  /**
   * Message to validate against the provided `schema_spec`.
   *
   * @var string
   */
  public $message;
  /**
   * Name of the schema against which to validate. Format is
   * `projects/{project}/schemas/{schema}`.
   *
   * @var string
   */
  public $name;
  protected $schemaType = Schema::class;
  protected $schemaDataType = '';

  /**
   * The encoding expected for messages
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
   * Message to validate against the provided `schema_spec`.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Name of the schema against which to validate. Format is
   * `projects/{project}/schemas/{schema}`.
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
   * Ad-hoc schema against which to validate
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateMessageRequest::class, 'Google_Service_Pubsub_ValidateMessageRequest');
