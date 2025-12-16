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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1GoogleDriveSourceResourceId extends \Google\Model
{
  /**
   * Unspecified resource type.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * File resource type.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_FILE = 'RESOURCE_TYPE_FILE';
  /**
   * Folder resource type.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_FOLDER = 'RESOURCE_TYPE_FOLDER';
  /**
   * Required. The ID of the Google Drive resource.
   *
   * @var string
   */
  public $resourceId;
  /**
   * Required. The type of the Google Drive resource.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Required. The ID of the Google Drive resource.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * Required. The type of the Google Drive resource.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, RESOURCE_TYPE_FILE,
   * RESOURCE_TYPE_FOLDER
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GoogleDriveSourceResourceId::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GoogleDriveSourceResourceId');
