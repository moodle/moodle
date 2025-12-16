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

namespace Google\Service\Meet;

class DriveDestination extends \Google\Model
{
  /**
   * Output only. Link used to play back the recording file in the browser. For
   * example, `https://drive.google.com/file/d/{$fileId}/view`.
   *
   * @var string
   */
  public $exportUri;
  /**
   * Output only. The `fileId` for the underlying MP4 file. For example,
   * "1kuceFZohVoCh6FulBHxwy6I15Ogpc4hP". Use `$ GET
   * https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media` to download
   * the blob. For more information, see
   * https://developers.google.com/drive/api/v3/reference/files/get.
   *
   * @var string
   */
  public $file;

  /**
   * Output only. Link used to play back the recording file in the browser. For
   * example, `https://drive.google.com/file/d/{$fileId}/view`.
   *
   * @param string $exportUri
   */
  public function setExportUri($exportUri)
  {
    $this->exportUri = $exportUri;
  }
  /**
   * @return string
   */
  public function getExportUri()
  {
    return $this->exportUri;
  }
  /**
   * Output only. The `fileId` for the underlying MP4 file. For example,
   * "1kuceFZohVoCh6FulBHxwy6I15Ogpc4hP". Use `$ GET
   * https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media` to download
   * the blob. For more information, see
   * https://developers.google.com/drive/api/v3/reference/files/get.
   *
   * @param string $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }
  /**
   * @return string
   */
  public function getFile()
  {
    return $this->file;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveDestination::class, 'Google_Service_Meet_DriveDestination');
