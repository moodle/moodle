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

namespace Google\Service\CloudRedis;

class ExportBackupRequest extends \Google\Model
{
  /**
   * Google Cloud Storage bucket, like "my-bucket".
   *
   * @var string
   */
  public $gcsBucket;

  /**
   * Google Cloud Storage bucket, like "my-bucket".
   *
   * @param string $gcsBucket
   */
  public function setGcsBucket($gcsBucket)
  {
    $this->gcsBucket = $gcsBucket;
  }
  /**
   * @return string
   */
  public function getGcsBucket()
  {
    return $this->gcsBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportBackupRequest::class, 'Google_Service_CloudRedis_ExportBackupRequest');
