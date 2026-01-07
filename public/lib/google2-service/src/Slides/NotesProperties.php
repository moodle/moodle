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

class NotesProperties extends \Google\Model
{
  /**
   * The object ID of the shape on this notes page that contains the speaker
   * notes for the corresponding slide. The actual shape may not always exist on
   * the notes page. Inserting text using this object ID will automatically
   * create the shape. In this case, the actual shape may have different object
   * ID. The `GetPresentation` or `GetPage` action will always return the latest
   * object ID.
   *
   * @var string
   */
  public $speakerNotesObjectId;

  /**
   * The object ID of the shape on this notes page that contains the speaker
   * notes for the corresponding slide. The actual shape may not always exist on
   * the notes page. Inserting text using this object ID will automatically
   * create the shape. In this case, the actual shape may have different object
   * ID. The `GetPresentation` or `GetPage` action will always return the latest
   * object ID.
   *
   * @param string $speakerNotesObjectId
   */
  public function setSpeakerNotesObjectId($speakerNotesObjectId)
  {
    $this->speakerNotesObjectId = $speakerNotesObjectId;
  }
  /**
   * @return string
   */
  public function getSpeakerNotesObjectId()
  {
    return $this->speakerNotesObjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotesProperties::class, 'Google_Service_Slides_NotesProperties');
