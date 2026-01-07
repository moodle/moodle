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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest extends \Google\Collection
{
  /**
   * Unspecified file format
   */
  public const FILE_FORMAT_FILE_FORMAT_UNSPECIFIED = 'FILE_FORMAT_UNSPECIFIED';
  /**
   * JSON File Format
   */
  public const FILE_FORMAT_JSON = 'JSON';
  /**
   * YAML File Format
   */
  public const FILE_FORMAT_YAML = 'YAML';
  protected $collection_key = 'apiTriggerResources';
  protected $apiTriggerResourcesType = GoogleCloudIntegrationsV1alphaApiTriggerResource::class;
  protected $apiTriggerResourcesDataType = 'array';
  /**
   * Required. File format for generated spec.
   *
   * @var string
   */
  public $fileFormat;

  /**
   * Required. List of api triggers
   *
   * @param GoogleCloudIntegrationsV1alphaApiTriggerResource[] $apiTriggerResources
   */
  public function setApiTriggerResources($apiTriggerResources)
  {
    $this->apiTriggerResources = $apiTriggerResources;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaApiTriggerResource[]
   */
  public function getApiTriggerResources()
  {
    return $this->apiTriggerResources;
  }
  /**
   * Required. File format for generated spec.
   *
   * Accepted values: FILE_FORMAT_UNSPECIFIED, JSON, YAML
   *
   * @param self::FILE_FORMAT_* $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return self::FILE_FORMAT_*
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest');
