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

namespace Google\Service\StorageBatchOperations;

class Manifest extends \Google\Model
{
  /**
   * Required. `manifest_location` must contain the manifest source file that is
   * a CSV file in a Google Cloud Storage bucket. Each row in the file must
   * include the object details i.e. BucketId and Name. Generation may
   * optionally be specified. When it is not specified the live object is acted
   * upon. `manifest_location` should either be 1) An absolute path to the
   * object in the format of `gs://bucket_name/path/file_name.csv`. 2) An
   * absolute path with a single wildcard character in the file name, for
   * example `gs://bucket_name/path/file_name*.csv`. If manifest location is
   * specified with a wildcard, objects in all manifest files matching the
   * pattern will be acted upon.
   *
   * @var string
   */
  public $manifestLocation;

  /**
   * Required. `manifest_location` must contain the manifest source file that is
   * a CSV file in a Google Cloud Storage bucket. Each row in the file must
   * include the object details i.e. BucketId and Name. Generation may
   * optionally be specified. When it is not specified the live object is acted
   * upon. `manifest_location` should either be 1) An absolute path to the
   * object in the format of `gs://bucket_name/path/file_name.csv`. 2) An
   * absolute path with a single wildcard character in the file name, for
   * example `gs://bucket_name/path/file_name*.csv`. If manifest location is
   * specified with a wildcard, objects in all manifest files matching the
   * pattern will be acted upon.
   *
   * @param string $manifestLocation
   */
  public function setManifestLocation($manifestLocation)
  {
    $this->manifestLocation = $manifestLocation;
  }
  /**
   * @return string
   */
  public function getManifestLocation()
  {
    return $this->manifestLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Manifest::class, 'Google_Service_StorageBatchOperations_Manifest');
