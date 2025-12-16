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

class GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse extends \Google\Collection
{
  protected $collection_key = 'files';
  protected $filesType = GoogleCloudIntegrationsV1alphaFile::class;
  protected $filesDataType = 'array';

  /**
   * List containing JSON for multiple file with type information.
   *
   * @param GoogleCloudIntegrationsV1alphaFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaFile[]
   */
  public function getFiles()
  {
    return $this->files;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse');
