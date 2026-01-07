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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Definition extends \Google\Model
{
  /**
   * Definition type unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Definition type schema.
   */
  public const TYPE_SCHEMA = 'SCHEMA';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  /**
   * Output only. The time at which the definition was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The name of the definition. Format: `projects/{project}/locatio
   * ns/{location}/apis/{api}/versions/{version}/definitions/{definition}`
   *
   * @var string
   */
  public $name;
  protected $schemaType = GoogleCloudApihubV1Schema::class;
  protected $schemaDataType = '';
  /**
   * Output only. The name of the spec from where the definition was parsed.
   * Format is `projects/{project}/locations/{location}/apis/{api}/versions/{ver
   * sion}/specs/{spec}`
   *
   * @var string
   */
  public $spec;
  /**
   * Output only. The type of the definition.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time at which the definition was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The list of user defined attributes associated with the
   * definition resource. The key is the attribute name. It will be of the
   * format: `projects/{project}/locations/{location}/attributes/{attribute}`.
   * The value is the attribute values associated with the resource.
   *
   * @param GoogleCloudApihubV1AttributeValues[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Output only. The time at which the definition was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier. The name of the definition. Format: `projects/{project}/locatio
   * ns/{location}/apis/{api}/versions/{version}/definitions/{definition}`
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
   * Output only. The value of a schema definition.
   *
   * @param GoogleCloudApihubV1Schema $schema
   */
  public function setSchema(GoogleCloudApihubV1Schema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudApihubV1Schema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Output only. The name of the spec from where the definition was parsed.
   * Format is `projects/{project}/locations/{location}/apis/{api}/versions/{ver
   * sion}/specs/{spec}`
   *
   * @param string $spec
   */
  public function setSpec($spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return string
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * Output only. The type of the definition.
   *
   * Accepted values: TYPE_UNSPECIFIED, SCHEMA
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
  /**
   * Output only. The time at which the definition was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Definition::class, 'Google_Service_APIhub_GoogleCloudApihubV1Definition');
