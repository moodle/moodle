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

class GcsBackupSource extends \Google\Collection
{
  protected $collection_key = 'uris';
  /**
   * Optional. URIs of the Cloud Storage objects to import. Example:
   * gs://bucket1/object1, gs://bucket2/folder2/object2
   *
   * @var string[]
   */
  public $uris;

  /**
   * Optional. URIs of the Cloud Storage objects to import. Example:
   * gs://bucket1/object1, gs://bucket2/folder2/object2
   *
   * @param string[] $uris
   */
  public function setUris($uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return string[]
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcsBackupSource::class, 'Google_Service_CloudRedis_GcsBackupSource');
