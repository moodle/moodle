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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2SourceCode extends \Google\Model
{
  protected $cloudStorageSourceType = GoogleCloudRunV2CloudStorageSource::class;
  protected $cloudStorageSourceDataType = '';

  /**
   * The source is a Cloud Storage bucket.
   *
   * @param GoogleCloudRunV2CloudStorageSource $cloudStorageSource
   */
  public function setCloudStorageSource(GoogleCloudRunV2CloudStorageSource $cloudStorageSource)
  {
    $this->cloudStorageSource = $cloudStorageSource;
  }
  /**
   * @return GoogleCloudRunV2CloudStorageSource
   */
  public function getCloudStorageSource()
  {
    return $this->cloudStorageSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2SourceCode::class, 'Google_Service_CloudRun_GoogleCloudRunV2SourceCode');
