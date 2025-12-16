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

class GoogleCloudApihubV1ApiOperation extends \Google\Collection
{
  protected $collection_key = 'sourceMetadata';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  /**
   * Output only. The time at which the operation was created.
   *
   * @var string
   */
  public $createTime;
  protected $detailsType = GoogleCloudApihubV1OperationDetails::class;
  protected $detailsDataType = '';
  /**
   * Identifier. The name of the operation. Format: `projects/{project}/location
   * s/{location}/apis/{api}/versions/{version}/operations/{operation}`
   *
   * @var string
   */
  public $name;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = 'array';
  /**
   * Output only. The name of the spec will be of the format: `projects/{project
   * }/locations/{location}/apis/{api}/versions/{version}/specs/{spec}` Note:The
   * name of the spec will be empty if the operation is created via
   * CreateApiOperation API.
   *
   * @var string
   */
  public $spec;
  /**
   * Output only. The time at which the operation was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The list of user defined attributes associated with the API
   * operation resource. The key is the attribute name. It will be of the
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
   * Output only. The time at which the operation was created.
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
   * Optional. Operation details. Note: Even though this field is optional, it
   * is required for CreateApiOperation API and we will fail the request if not
   * provided.
   *
   * @param GoogleCloudApihubV1OperationDetails $details
   */
  public function setDetails(GoogleCloudApihubV1OperationDetails $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleCloudApihubV1OperationDetails
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Identifier. The name of the operation. Format: `projects/{project}/location
   * s/{location}/apis/{api}/versions/{version}/operations/{operation}`
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
   * Output only. The list of sources and metadata from the sources of the API
   * operation.
   *
   * @param GoogleCloudApihubV1SourceMetadata[] $sourceMetadata
   */
  public function setSourceMetadata($sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return GoogleCloudApihubV1SourceMetadata[]
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * Output only. The name of the spec will be of the format: `projects/{project
   * }/locations/{location}/apis/{api}/versions/{version}/specs/{spec}` Note:The
   * name of the spec will be empty if the operation is created via
   * CreateApiOperation API.
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
   * Output only. The time at which the operation was last updated.
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
class_alias(GoogleCloudApihubV1ApiOperation::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApiOperation');
