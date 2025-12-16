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

namespace Google\Service\Slides;

class SlideProperties extends \Google\Model
{
  /**
   * Whether the slide is skipped in the presentation mode. Defaults to false.
   *
   * @var bool
   */
  public $isSkipped;
  /**
   * The object ID of the layout that this slide is based on. This property is
   * read-only.
   *
   * @var string
   */
  public $layoutObjectId;
  /**
   * The object ID of the master that this slide is based on. This property is
   * read-only.
   *
   * @var string
   */
  public $masterObjectId;
  protected $notesPageType = Page::class;
  protected $notesPageDataType = '';

  /**
   * Whether the slide is skipped in the presentation mode. Defaults to false.
   *
   * @param bool $isSkipped
   */
  public function setIsSkipped($isSkipped)
  {
    $this->isSkipped = $isSkipped;
  }
  /**
   * @return bool
   */
  public function getIsSkipped()
  {
    return $this->isSkipped;
  }
  /**
   * The object ID of the layout that this slide is based on. This property is
   * read-only.
   *
   * @param string $layoutObjectId
   */
  public function setLayoutObjectId($layoutObjectId)
  {
    $this->layoutObjectId = $layoutObjectId;
  }
  /**
   * @return string
   */
  public function getLayoutObjectId()
  {
    return $this->layoutObjectId;
  }
  /**
   * The object ID of the master that this slide is based on. This property is
   * read-only.
   *
   * @param string $masterObjectId
   */
  public function setMasterObjectId($masterObjectId)
  {
    $this->masterObjectId = $masterObjectId;
  }
  /**
   * @return string
   */
  public function getMasterObjectId()
  {
    return $this->masterObjectId;
  }
  /**
   * The notes page that this slide is associated with. It defines the visual
   * appearance of a notes page when printing or exporting slides with speaker
   * notes. A notes page inherits properties from the notes master. The
   * placeholder shape with type BODY on the notes page contains the speaker
   * notes for this slide. The ID of this shape is identified by the
   * speakerNotesObjectId field. The notes page is read-only except for the text
   * content and styles of the speaker notes shape. This property is read-only.
   *
   * @param Page $notesPage
   */
  public function setNotesPage(Page $notesPage)
  {
    $this->notesPage = $notesPage;
  }
  /**
   * @return Page
   */
  public function getNotesPage()
  {
    return $this->notesPage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlideProperties::class, 'Google_Service_Slides_SlideProperties');
