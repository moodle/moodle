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

class UpdateSlidesPositionRequest extends \Google\Collection
{
  protected $collection_key = 'slideObjectIds';
  /**
   * The index where the slides should be inserted, based on the slide
   * arrangement before the move takes place. Must be between zero and the
   * number of slides in the presentation, inclusive.
   *
   * @var int
   */
  public $insertionIndex;
  /**
   * The IDs of the slides in the presentation that should be moved. The slides
   * in this list must be in existing presentation order, without duplicates.
   *
   * @var string[]
   */
  public $slideObjectIds;

  /**
   * The index where the slides should be inserted, based on the slide
   * arrangement before the move takes place. Must be between zero and the
   * number of slides in the presentation, inclusive.
   *
   * @param int $insertionIndex
   */
  public function setInsertionIndex($insertionIndex)
  {
    $this->insertionIndex = $insertionIndex;
  }
  /**
   * @return int
   */
  public function getInsertionIndex()
  {
    return $this->insertionIndex;
  }
  /**
   * The IDs of the slides in the presentation that should be moved. The slides
   * in this list must be in existing presentation order, without duplicates.
   *
   * @param string[] $slideObjectIds
   */
  public function setSlideObjectIds($slideObjectIds)
  {
    $this->slideObjectIds = $slideObjectIds;
  }
  /**
   * @return string[]
   */
  public function getSlideObjectIds()
  {
    return $this->slideObjectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSlidesPositionRequest::class, 'Google_Service_Slides_UpdateSlidesPositionRequest');
