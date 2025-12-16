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

namespace Google\Service\Dataform;

class FileOperation extends \Google\Model
{
  protected $deleteFileType = DeleteFile::class;
  protected $deleteFileDataType = '';
  protected $writeFileType = WriteFile::class;
  protected $writeFileDataType = '';

  /**
   * Represents the delete operation.
   *
   * @param DeleteFile $deleteFile
   */
  public function setDeleteFile(DeleteFile $deleteFile)
  {
    $this->deleteFile = $deleteFile;
  }
  /**
   * @return DeleteFile
   */
  public function getDeleteFile()
  {
    return $this->deleteFile;
  }
  /**
   * Represents the write operation.
   *
   * @param WriteFile $writeFile
   */
  public function setWriteFile(WriteFile $writeFile)
  {
    $this->writeFile = $writeFile;
  }
  /**
   * @return WriteFile
   */
  public function getWriteFile()
  {
    return $this->writeFile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileOperation::class, 'Google_Service_Dataform_FileOperation');
