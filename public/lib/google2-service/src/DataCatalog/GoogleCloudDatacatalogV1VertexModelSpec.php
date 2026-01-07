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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1VertexModelSpec extends \Google\Collection
{
  protected $collection_key = 'versionAliases';
  /**
   * URI of the Docker image to be used as the custom container for serving
   * predictions.
   *
   * @var string
   */
  public $containerImageUri;
  /**
   * User provided version aliases so that a model version can be referenced via
   * alias
   *
   * @var string[]
   */
  public $versionAliases;
  /**
   * The description of this version.
   *
   * @var string
   */
  public $versionDescription;
  /**
   * The version ID of the model.
   *
   * @var string
   */
  public $versionId;
  protected $vertexModelSourceInfoType = GoogleCloudDatacatalogV1VertexModelSourceInfo::class;
  protected $vertexModelSourceInfoDataType = '';

  /**
   * URI of the Docker image to be used as the custom container for serving
   * predictions.
   *
   * @param string $containerImageUri
   */
  public function setContainerImageUri($containerImageUri)
  {
    $this->containerImageUri = $containerImageUri;
  }
  /**
   * @return string
   */
  public function getContainerImageUri()
  {
    return $this->containerImageUri;
  }
  /**
   * User provided version aliases so that a model version can be referenced via
   * alias
   *
   * @param string[] $versionAliases
   */
  public function setVersionAliases($versionAliases)
  {
    $this->versionAliases = $versionAliases;
  }
  /**
   * @return string[]
   */
  public function getVersionAliases()
  {
    return $this->versionAliases;
  }
  /**
   * The description of this version.
   *
   * @param string $versionDescription
   */
  public function setVersionDescription($versionDescription)
  {
    $this->versionDescription = $versionDescription;
  }
  /**
   * @return string
   */
  public function getVersionDescription()
  {
    return $this->versionDescription;
  }
  /**
   * The version ID of the model.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
  /**
   * Source of a Vertex model.
   *
   * @param GoogleCloudDatacatalogV1VertexModelSourceInfo $vertexModelSourceInfo
   */
  public function setVertexModelSourceInfo(GoogleCloudDatacatalogV1VertexModelSourceInfo $vertexModelSourceInfo)
  {
    $this->vertexModelSourceInfo = $vertexModelSourceInfo;
  }
  /**
   * @return GoogleCloudDatacatalogV1VertexModelSourceInfo
   */
  public function getVertexModelSourceInfo()
  {
    return $this->vertexModelSourceInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1VertexModelSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1VertexModelSpec');
