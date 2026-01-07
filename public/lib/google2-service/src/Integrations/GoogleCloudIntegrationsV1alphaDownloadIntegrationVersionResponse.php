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

class GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse extends \Google\Collection
{
  protected $collection_key = 'files';
  /**
   * String representation of the requested file.
   *
   * @var string
   */
  public $content;
  protected $filesType = GoogleCloudIntegrationsV1alphaSerializedFile::class;
  protected $filesDataType = 'array';

  /**
   * String representation of the requested file.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * List containing String represendation for multiple file with type.
   *
   * @param GoogleCloudIntegrationsV1alphaSerializedFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaSerializedFile[]
   */
  public function getFiles()
  {
    return $this->files;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse');
