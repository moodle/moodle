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

class Create extends \Google\Model
{
  protected $copyType = Copy::class;
  protected $copyDataType = '';
  protected $newType = DriveactivityNew::class;
  protected $newDataType = '';
  protected $uploadType = Upload::class;
  protected $uploadDataType = '';

  /**
   * If present, indicates the object was created by copying an existing Drive
   * object.
   *
   * @param Copy $copy
   */
  public function setCopy(Copy $copy)
  {
    $this->copy = $copy;
  }
  /**
   * @return Copy
   */
  public function getCopy()
  {
    return $this->copy;
  }
  /**
   * If present, indicates the object was newly created (e.g. as a blank
   * document), not derived from a Drive object or external object.
   *
   * @param DriveactivityNew $new
   */
  public function setNew(DriveactivityNew $new)
  {
    $this->new = $new;
  }
  /**
   * @return DriveactivityNew
   */
  public function getNew()
  {
    return $this->new;
  }
  /**
   * If present, indicates the object originated externally and was uploaded to
   * Drive.
   *
   * @param Upload $upload
   */
  public function setUpload(Upload $upload)
  {
    $this->upload = $upload;
  }
  /**
   * @return Upload
   */
  public function getUpload()
  {
    return $this->upload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Create::class, 'Google_Service_DriveActivity_Create');
