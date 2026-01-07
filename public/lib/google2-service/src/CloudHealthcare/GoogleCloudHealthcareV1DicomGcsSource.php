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

namespace Google\Service\CloudHealthcare;

class GoogleCloudHealthcareV1DicomGcsSource extends \Google\Model
{
  /**
   * Points to a Cloud Storage URI containing file(s) with content only. The URI
   * must be in the following format: `gs://{bucket_id}/{object_id}`. The URI
   * can include wildcards in `object_id` and thus identify multiple files.
   * Supported wildcards: * '*' to match 0 or more non-separator characters *
   * '**' to match 0 or more characters (including separators). Must be used at
   * the end of a path and with no other wildcards in the path. Can also be used
   * with a file extension (such as .dcm), which imports all files with the
   * extension in the specified directory and its sub-directories. For example,
   * `gs://my-bucket/my-directory*.dcm` imports all files with .dcm extensions
   * in `my-directory/` and its sub-directories. * '?' to match 1 character. All
   * other URI formats are invalid. Files matching the wildcard are expected to
   * contain content only, no metadata.
   *
   * @var string
   */
  public $uri;

  /**
   * Points to a Cloud Storage URI containing file(s) with content only. The URI
   * must be in the following format: `gs://{bucket_id}/{object_id}`. The URI
   * can include wildcards in `object_id` and thus identify multiple files.
   * Supported wildcards: * '*' to match 0 or more non-separator characters *
   * '**' to match 0 or more characters (including separators). Must be used at
   * the end of a path and with no other wildcards in the path. Can also be used
   * with a file extension (such as .dcm), which imports all files with the
   * extension in the specified directory and its sub-directories. For example,
   * `gs://my-bucket/my-directory*.dcm` imports all files with .dcm extensions
   * in `my-directory/` and its sub-directories. * '?' to match 1 character. All
   * other URI formats are invalid. Files matching the wildcard are expected to
   * contain content only, no metadata.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudHealthcareV1DicomGcsSource::class, 'Google_Service_CloudHealthcare_GoogleCloudHealthcareV1DicomGcsSource');
