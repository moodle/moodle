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

class Schema extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A Protocol Buffer schema definition.
   */
  public const TYPE_PROTOCOL_BUFFER = 'PROTOCOL_BUFFER';
  /**
   * An Avro schema definition.
   */
  public const TYPE_AVRO = 'AVRO';
  /**
   * The definition of the schema. This should contain a string representing the
   * full definition of the schema that is a valid schema definition of the type
   * specified in `type`.
   *
   * @var string
   */
  public $definition;
  /**
   * Required. Name of the schema. Format is
   * `projects/{project}/schemas/{schema}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. Immutable. The revision ID of the schema.
   *
   * @var string
   */
  public $revisionId;
  /**
   * The type of the schema definition.
   *
   * @var string
   */
  public $type;

  /**
   * The definition of the schema. This should contain a string representing the
   * full definition of the schema that is a valid schema definition of the type
   * specified in `type`.
   *
   * @param string $definition
   */
  public function setDefinition($definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return string
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * Required. Name of the schema. Format is
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
   * Output only. The timestamp that the revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. Immutable. The revision ID of the schema.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * The type of the schema definition.
   *
   * Accepted values: TYPE_UNSPECIFIED, PROTOCOL_BUFFER, AVRO
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schema::class, 'Google_Service_Pubsub_Schema');
