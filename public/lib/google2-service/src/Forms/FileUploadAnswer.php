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

namespace Google\Service\Forms;

class FileUploadAnswer extends \Google\Model
{
  /**
   * Output only. The ID of the Google Drive file.
   *
   * @var string
   */
  public $fileId;
  /**
   * Output only. The file name, as stored in Google Drive on upload.
   *
   * @var string
   */
  public $fileName;
  /**
   * Output only. The MIME type of the file, as stored in Google Drive on
   * upload.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Output only. The ID of the Google Drive file.
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
  /**
   * Output only. The file name, as stored in Google Drive on upload.
   *
   * @param string $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return string
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * Output only. The MIME type of the file, as stored in Google Drive on
   * upload.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileUploadAnswer::class, 'Google_Service_Forms_FileUploadAnswer');
