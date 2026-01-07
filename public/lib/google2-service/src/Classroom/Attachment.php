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

namespace Google\Service\Classroom;

class Attachment extends \Google\Model
{
  protected $driveFileType = DriveFile::class;
  protected $driveFileDataType = '';
  protected $formType = Form::class;
  protected $formDataType = '';
  protected $linkType = Link::class;
  protected $linkDataType = '';
  protected $youTubeVideoType = YouTubeVideo::class;
  protected $youTubeVideoDataType = '';

  /**
   * Google Drive file attachment.
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
   * Google Forms attachment.
   *
   * @param Form $form
   */
  public function setForm(Form $form)
  {
    $this->form = $form;
  }
  /**
   * @return Form
   */
  public function getForm()
  {
    return $this->form;
  }
  /**
   * Link attachment.
   *
   * @param Link $link
   */
  public function setLink(Link $link)
  {
    $this->link = $link;
  }
  /**
   * @return Link
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Youtube video attachment.
   *
   * @param YouTubeVideo $youTubeVideo
   */
  public function setYouTubeVideo(YouTubeVideo $youTubeVideo)
  {
    $this->youTubeVideo = $youTubeVideo;
  }
  /**
   * @return YouTubeVideo
   */
  public function getYouTubeVideo()
  {
    return $this->youTubeVideo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attachment::class, 'Google_Service_Classroom_Attachment');
