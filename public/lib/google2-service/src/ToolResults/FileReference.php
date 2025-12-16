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

namespace Google\Service\ToolResults;

class FileReference extends \Google\Model
{
  /**
   * The URI of a file stored in Google Cloud Storage. For example:
   * http://storage.googleapis.com/mybucket/path/to/test.xml or in gsutil
   * format: gs://mybucket/path/to/test.xml with version-specific info,
   * gs://mybucket/path/to/test.xml#1360383693690000 An INVALID_ARGUMENT error
   * will be returned if the URI format is not supported. - In response: always
   * set - In create/update request: always set
   *
   * @var string
   */
  public $fileUri;

  /**
   * The URI of a file stored in Google Cloud Storage. For example:
   * http://storage.googleapis.com/mybucket/path/to/test.xml or in gsutil
   * format: gs://mybucket/path/to/test.xml with version-specific info,
   * gs://mybucket/path/to/test.xml#1360383693690000 An INVALID_ARGUMENT error
   * will be returned if the URI format is not supported. - In response: always
   * set - In create/update request: always set
   *
   * @param string $fileUri
   */
  public function setFileUri($fileUri)
  {
    $this->fileUri = $fileUri;
  }
  /**
   * @return string
   */
  public function getFileUri()
  {
    return $this->fileUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileReference::class, 'Google_Service_ToolResults_FileReference');
