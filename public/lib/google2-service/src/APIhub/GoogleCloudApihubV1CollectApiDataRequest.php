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

class GoogleCloudApihubV1CollectApiDataRequest extends \Google\Model
{
  /**
   * The default value. This value is used if the collection type is omitted.
   */
  public const COLLECTION_TYPE_COLLECTION_TYPE_UNSPECIFIED = 'COLLECTION_TYPE_UNSPECIFIED';
  /**
   * The collection type is upsert. This should be used when an API is created
   * or updated at the source.
   */
  public const COLLECTION_TYPE_COLLECTION_TYPE_UPSERT = 'COLLECTION_TYPE_UPSERT';
  /**
   * The collection type is delete. This should be used when an API is deleted
   * at the source.
   */
  public const COLLECTION_TYPE_COLLECTION_TYPE_DELETE = 'COLLECTION_TYPE_DELETE';
  /**
   * Required. The action ID to be used for collecting the API data. This should
   * map to one of the action IDs specified in action configs in the plugin.
   *
   * @var string
   */
  public $actionId;
  protected $apiDataType = GoogleCloudApihubV1ApiData::class;
  protected $apiDataDataType = '';
  /**
   * Required. The type of collection. Applies to all entries in api_data.
   *
   * @var string
   */
  public $collectionType;
  /**
   * Required. The plugin instance collecting the API data. Format: `projects/{p
   * roject}/locations/{location}/plugins/{plugin}/instances/{instance}`.
   *
   * @var string
   */
  public $pluginInstance;

  /**
   * Required. The action ID to be used for collecting the API data. This should
   * map to one of the action IDs specified in action configs in the plugin.
   *
   * @param string $actionId
   */
  public function setActionId($actionId)
  {
    $this->actionId = $actionId;
  }
  /**
   * @return string
   */
  public function getActionId()
  {
    return $this->actionId;
  }
  /**
   * Required. The API data to be collected.
   *
   * @param GoogleCloudApihubV1ApiData $apiData
   */
  public function setApiData(GoogleCloudApihubV1ApiData $apiData)
  {
    $this->apiData = $apiData;
  }
  /**
   * @return GoogleCloudApihubV1ApiData
   */
  public function getApiData()
  {
    return $this->apiData;
  }
  /**
   * Required. The type of collection. Applies to all entries in api_data.
   *
   * Accepted values: COLLECTION_TYPE_UNSPECIFIED, COLLECTION_TYPE_UPSERT,
   * COLLECTION_TYPE_DELETE
   *
   * @param self::COLLECTION_TYPE_* $collectionType
   */
  public function setCollectionType($collectionType)
  {
    $this->collectionType = $collectionType;
  }
  /**
   * @return self::COLLECTION_TYPE_*
   */
  public function getCollectionType()
  {
    return $this->collectionType;
  }
  /**
   * Required. The plugin instance collecting the API data. Format: `projects/{p
   * roject}/locations/{location}/plugins/{plugin}/instances/{instance}`.
   *
   * @param string $pluginInstance
   */
  public function setPluginInstance($pluginInstance)
  {
    $this->pluginInstance = $pluginInstance;
  }
  /**
   * @return string
   */
  public function getPluginInstance()
  {
    return $this->pluginInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1CollectApiDataRequest::class, 'Google_Service_APIhub_GoogleCloudApihubV1CollectApiDataRequest');
