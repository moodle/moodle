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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class ExtendSchemaRequest extends \Google\Model
{
  /**
   * Required. Description for Schema Change.
   *
   * @var string
   */
  public $description;
  /**
   * File uploaded as a byte stream input.
   *
   * @var string
   */
  public $fileContents;
  /**
   * File stored in Cloud Storage bucket and represented in the form
   * projects/{project_id}/buckets/{bucket_name}/objects/{object_name} File
   * should be in the same project as the domain.
   *
   * @var string
   */
  public $gcsPath;

  /**
   * Required. Description for Schema Change.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * File uploaded as a byte stream input.
   *
   * @param string $fileContents
   */
  public function setFileContents($fileContents)
  {
    $this->fileContents = $fileContents;
  }
  /**
   * @return string
   */
  public function getFileContents()
  {
    return $this->fileContents;
  }
  /**
   * File stored in Cloud Storage bucket and represented in the form
   * projects/{project_id}/buckets/{bucket_name}/objects/{object_name} File
   * should be in the same project as the domain.
   *
   * @param string $gcsPath
   */
  public function setGcsPath($gcsPath)
  {
    $this->gcsPath = $gcsPath;
  }
  /**
   * @return string
   */
  public function getGcsPath()
  {
    return $this->gcsPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtendSchemaRequest::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_ExtendSchemaRequest');
