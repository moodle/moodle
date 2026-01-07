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

namespace Google\Service\DriveActivity;

class DriveItemReference extends \Google\Model
{
  protected $driveFileType = DriveFile::class;
  protected $driveFileDataType = '';
  protected $driveFolderType = DriveFolder::class;
  protected $driveFolderDataType = '';
  protected $fileType = DriveactivityFile::class;
  protected $fileDataType = '';
  protected $folderType = Folder::class;
  protected $folderDataType = '';
  /**
   * The target Drive item. The format is `items/ITEM_ID`.
   *
   * @var string
   */
  public $name;
  /**
   * The title of the Drive item.
   *
   * @var string
   */
  public $title;

  /**
   * The Drive item is a file.
   *
   * @param DriveFile $driveFile
   */
  public function setDriveFile(DriveFile $driveFile)
  {
    $this->driveFile = $driveFile;
  }
  /**
   * @return DriveFile
   */
  public function getDriveFile()
  {
    return $this->driveFile;
  }
  /**
   * The Drive item is a folder. Includes information about the type of folder.
   *
   * @param DriveFolder $driveFolder
   */
  public function setDriveFolder(DriveFolder $driveFolder)
  {
    $this->driveFolder = $driveFolder;
  }
  /**
   * @return DriveFolder
   */
  public function getDriveFolder()
  {
    return $this->driveFolder;
  }
  /**
   * This field is deprecated; please use the `driveFile` field instead.
   *
   * @deprecated
   * @param DriveactivityFile $file
   */
  public function setFile(DriveactivityFile $file)
  {
    $this->file = $file;
  }
  /**
   * @deprecated
   * @return DriveactivityFile
   */
  public function getFile()
  {
    return $this->file;
  }
  /**
   * This field is deprecated; please use the `driveFolder` field instead.
   *
   * @deprecated
   * @param Folder $folder
   */
  public function setFolder(Folder $folder)
  {
    $this->folder = $folder;
  }
  /**
   * @deprecated
   * @return Folder
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * The target Drive item. The format is `items/ITEM_ID`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The title of the Drive item.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveItemReference::class, 'Google_Service_DriveActivity_DriveItemReference');
