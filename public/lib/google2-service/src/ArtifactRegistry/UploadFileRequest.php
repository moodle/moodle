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

namespace Google\Service\ArtifactRegistry;

class UploadFileRequest extends \Google\Model
{
  /**
   * Optional. The ID of the file. If left empty will default to sha256 digest
   * of the content uploaded.
   *
   * @var string
   */
  public $fileId;

  /**
   * Optional. The ID of the file. If left empty will default to sha256 digest
   * of the content uploaded.
   *
   * @param string $fileId
   */
  public function setFileId($fileId)
  {
    $this->fileId = $fileId;
  }
  /**
   * @return string
   */
  public function getFileId()
  {
    return $this->fileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UploadFileRequest::class, 'Google_Service_ArtifactRegistry_UploadFileRequest');
