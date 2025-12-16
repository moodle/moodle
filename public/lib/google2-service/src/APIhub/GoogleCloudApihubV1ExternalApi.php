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

class GoogleCloudApihubV1ExternalApi extends \Google\Collection
{
  protected $collection_key = 'paths';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the external API. Max length is 2000 characters
   * (Unicode Code Points).
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name of the external API. Max length is 63 characters
   * (Unicode Code Points).
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  /**
   * Optional. List of endpoints on which this API is accessible.
   *
   * @var string[]
   */
  public $endpoints;
  /**
   * Identifier. Format:
   * `projects/{project}/locations/{location}/externalApi/{externalApi}`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. List of paths served by this API.
   *
   * @var string[]
   */
  public $paths;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The list of user defined attributes associated with the Version
   * resource. The key is the attribute name. It will be of the format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`. The value
   * is the attribute values associated with the resource.
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
   * Output only. Creation timestamp.
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
   * Optional. Description of the external API. Max length is 2000 characters
   * (Unicode Code Points).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Display name of the external API. Max length is 63 characters
   * (Unicode Code Points).
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
   * Optional. Documentation of the external API.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. List of endpoints on which this API is accessible.
   *
   * @param string[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return string[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Identifier. Format:
   * `projects/{project}/locations/{location}/externalApi/{externalApi}`.
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
   * Optional. List of paths served by this API.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
  /**
   * Output only. Last update timestamp.
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
class_alias(GoogleCloudApihubV1ExternalApi::class, 'Google_Service_APIhub_GoogleCloudApihubV1ExternalApi');
