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

class GoogleCloudDiscoveryengineV1alphaCollection extends \Google\Model
{
  /**
   * Output only. Timestamp the Collection was created at.
   *
   * @var string
   */
  public $createTime;
  protected $dataConnectorType = GoogleCloudDiscoveryengineV1alphaDataConnector::class;
  protected $dataConnectorDataType = '';
  /**
   * Required. The Collection display name. This field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The full resource name of the Collection. Format:
   * `projects/{project}/locations/{location}/collections/{collection_id}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Timestamp the Collection was created at.
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
   * Output only. The data connector, if present, manages the connection for
   * data stores in the Collection. To set up the connector, use
   * DataConnectorService.SetUpDataConnector method, which creates a new
   * Collection while setting up the DataConnector singleton resource. Setting
   * up connector on an existing Collection is not supported. This output only
   * field contains a subset of the DataConnector fields, including `name`,
   * `data_source`, `entities.entity_name` and `entities.data_store`. To get
   * more details about a data connector, use the
   * DataConnectorService.GetDataConnector method.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataConnector $dataConnector
   */
  public function setDataConnector(GoogleCloudDiscoveryengineV1alphaDataConnector $dataConnector)
  {
    $this->dataConnector = $dataConnector;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataConnector
   */
  public function getDataConnector()
  {
    return $this->dataConnector;
  }
  /**
   * Required. The Collection display name. This field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
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
   * Immutable. The full resource name of the Collection. Format:
   * `projects/{project}/locations/{location}/collections/{collection_id}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaCollection::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaCollection');
