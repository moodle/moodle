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

class GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest extends \Google\Model
{
  /**
   * Default unspecified action.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Upload or upsert data.
   */
  public const ACTION_UPLOAD = 'UPLOAD';
  /**
   * Delete data.
   */
  public const ACTION_DELETE = 'DELETE';
  /**
   * Default unspecified type.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Proxy deployment manifest.
   */
  public const DATA_TYPE_PROXY_DEPLOYMENT_MANIFEST = 'PROXY_DEPLOYMENT_MANIFEST';
  /**
   * Environment manifest.
   */
  public const DATA_TYPE_ENVIRONMENT_MANIFEST = 'ENVIRONMENT_MANIFEST';
  /**
   * Proxy bundle.
   */
  public const DATA_TYPE_PROXY_BUNDLE = 'PROXY_BUNDLE';
  /**
   * Shared flow bundle.
   */
  public const DATA_TYPE_SHARED_FLOW_BUNDLE = 'SHARED_FLOW_BUNDLE';
  /**
   * Required. Action to be performed.
   *
   * @var string
   */
  public $action;
  /**
   * Required. Data to be managed.
   *
   * @var string
   */
  public $data;
  /**
   * Required. Type of data to be managed.
   *
   * @var string
   */
  public $dataType;
  /**
   * Required. Relative path of data being managed for a given plugin instance.
   *
   * @var string
   */
  public $relativePath;

  /**
   * Required. Action to be performed.
   *
   * Accepted values: ACTION_UNSPECIFIED, UPLOAD, DELETE
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Required. Data to be managed.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Required. Type of data to be managed.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, PROXY_DEPLOYMENT_MANIFEST,
   * ENVIRONMENT_MANIFEST, PROXY_BUNDLE, SHARED_FLOW_BUNDLE
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Required. Relative path of data being managed for a given plugin instance.
   *
   * @param string $relativePath
   */
  public function setRelativePath($relativePath)
  {
    $this->relativePath = $relativePath;
  }
  /**
   * @return string
   */
  public function getRelativePath()
  {
    return $this->relativePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest::class, 'Google_Service_APIhub_GoogleCloudApihubV1ManagePluginInstanceSourceDataRequest');
