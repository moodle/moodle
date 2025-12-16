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

namespace Google\Service\TagManager;

class RevertFolderResponse extends \Google\Model
{
  protected $folderType = Folder::class;
  protected $folderDataType = '';

  /**
   * Folder as it appears in the latest container version since the last
   * workspace synchronization operation. If no folder is present, that means
   * the folder was deleted in the latest container version.
   *
   * @param Folder $folder
   */
  public function setFolder(Folder $folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return Folder
   */
  public function getFolder()
  {
    return $this->folder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevertFolderResponse::class, 'Google_Service_TagManager_RevertFolderResponse');
