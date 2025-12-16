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

class GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest extends \Google\Model
{
  /**
   * Required. The display name of the Collection. Should be human readable,
   * used to display collections in the Console Dashboard. UTF-8 encoded string
   * with limit of 1024 characters.
   *
   * @var string
   */
  public $collectionDisplayName;
  /**
   * Required. The ID to use for the Collection, which will become the final
   * component of the Collection's resource name. A new Collection is created as
   * part of the DataConnector setup. DataConnector is a singleton resource
   * under Collection, managing all DataStores of the Collection. This field
   * must conform to [RFC-1034](https://tools.ietf.org/html/rfc1034) standard
   * with a length limit of 63 characters. Otherwise, an INVALID_ARGUMENT error
   * is returned.
   *
   * @var string
   */
  public $collectionId;
  protected $dataConnectorType = GoogleCloudDiscoveryengineV1DataConnector::class;
  protected $dataConnectorDataType = '';

  /**
   * Required. The display name of the Collection. Should be human readable,
   * used to display collections in the Console Dashboard. UTF-8 encoded string
   * with limit of 1024 characters.
   *
   * @param string $collectionDisplayName
   */
  public function setCollectionDisplayName($collectionDisplayName)
  {
    $this->collectionDisplayName = $collectionDisplayName;
  }
  /**
   * @return string
   */
  public function getCollectionDisplayName()
  {
    return $this->collectionDisplayName;
  }
  /**
   * Required. The ID to use for the Collection, which will become the final
   * component of the Collection's resource name. A new Collection is created as
   * part of the DataConnector setup. DataConnector is a singleton resource
   * under Collection, managing all DataStores of the Collection. This field
   * must conform to [RFC-1034](https://tools.ietf.org/html/rfc1034) standard
   * with a length limit of 63 characters. Otherwise, an INVALID_ARGUMENT error
   * is returned.
   *
   * @param string $collectionId
   */
  public function setCollectionId($collectionId)
  {
    $this->collectionId = $collectionId;
  }
  /**
   * @return string
   */
  public function getCollectionId()
  {
    return $this->collectionId;
  }
  /**
   * Required. The DataConnector to initialize in the newly created Collection.
   *
   * @param GoogleCloudDiscoveryengineV1DataConnector $dataConnector
   */
  public function setDataConnector(GoogleCloudDiscoveryengineV1DataConnector $dataConnector)
  {
    $this->dataConnector = $dataConnector;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DataConnector
   */
  public function getDataConnector()
  {
    return $this->dataConnector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SetUpDataConnectorRequest');
