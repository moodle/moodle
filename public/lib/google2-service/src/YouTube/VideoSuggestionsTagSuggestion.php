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

namespace Google\Service\YouTube;

class VideoSuggestionsTagSuggestion extends \Google\Collection
{
  protected $collection_key = 'categoryRestricts';
  /**
   * A set of video categories for which the tag is relevant. You can use this
   * information to display appropriate tag suggestions based on the video
   * category that the video uploader associates with the video. By default, tag
   * suggestions are relevant for all categories if there are no restricts
   * defined for the keyword.
   *
   * @var string[]
   */
  public $categoryRestricts;
  /**
   * The keyword tag suggested for the video.
   *
   * @var string
   */
  public $tag;

  /**
   * A set of video categories for which the tag is relevant. You can use this
   * information to display appropriate tag suggestions based on the video
   * category that the video uploader associates with the video. By default, tag
   * suggestions are relevant for all categories if there are no restricts
   * defined for the keyword.
   *
   * @param string[] $categoryRestricts
   */
  public function setCategoryRestricts($categoryRestricts)
  {
    $this->categoryRestricts = $categoryRestricts;
  }
  /**
   * @return string[]
   */
  public function getCategoryRestricts()
  {
    return $this->categoryRestricts;
  }
  /**
   * The keyword tag suggested for the video.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoSuggestionsTagSuggestion::class, 'Google_Service_YouTube_VideoSuggestionsTagSuggestion');
