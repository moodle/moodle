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

class VideoSuggestions extends \Google\Collection
{
  protected $collection_key = 'tagSuggestions';
  /**
   * A list of video editing operations that might improve the video quality or
   * playback experience of the uploaded video.
   *
   * @var string[]
   */
  public $editorSuggestions;
  /**
   * A list of errors that will prevent YouTube from successfully processing the
   * uploaded video video. These errors indicate that, regardless of the video's
   * current processing status, eventually, that status will almost certainly be
   * failed.
   *
   * @var string[]
   */
  public $processingErrors;
  /**
   * A list of suggestions that may improve YouTube's ability to process the
   * video.
   *
   * @var string[]
   */
  public $processingHints;
  /**
   * A list of reasons why YouTube may have difficulty transcoding the uploaded
   * video or that might result in an erroneous transcoding. These warnings are
   * generated before YouTube actually processes the uploaded video file. In
   * addition, they identify issues that are unlikely to cause the video
   * processing to fail but that might cause problems such as sync issues, video
   * artifacts, or a missing audio track.
   *
   * @var string[]
   */
  public $processingWarnings;
  protected $tagSuggestionsType = VideoSuggestionsTagSuggestion::class;
  protected $tagSuggestionsDataType = 'array';

  /**
   * A list of video editing operations that might improve the video quality or
   * playback experience of the uploaded video.
   *
   * @param string[] $editorSuggestions
   */
  public function setEditorSuggestions($editorSuggestions)
  {
    $this->editorSuggestions = $editorSuggestions;
  }
  /**
   * @return string[]
   */
  public function getEditorSuggestions()
  {
    return $this->editorSuggestions;
  }
  /**
   * A list of errors that will prevent YouTube from successfully processing the
   * uploaded video video. These errors indicate that, regardless of the video's
   * current processing status, eventually, that status will almost certainly be
   * failed.
   *
   * @param string[] $processingErrors
   */
  public function setProcessingErrors($processingErrors)
  {
    $this->processingErrors = $processingErrors;
  }
  /**
   * @return string[]
   */
  public function getProcessingErrors()
  {
    return $this->processingErrors;
  }
  /**
   * A list of suggestions that may improve YouTube's ability to process the
   * video.
   *
   * @param string[] $processingHints
   */
  public function setProcessingHints($processingHints)
  {
    $this->processingHints = $processingHints;
  }
  /**
   * @return string[]
   */
  public function getProcessingHints()
  {
    return $this->processingHints;
  }
  /**
   * A list of reasons why YouTube may have difficulty transcoding the uploaded
   * video or that might result in an erroneous transcoding. These warnings are
   * generated before YouTube actually processes the uploaded video file. In
   * addition, they identify issues that are unlikely to cause the video
   * processing to fail but that might cause problems such as sync issues, video
   * artifacts, or a missing audio track.
   *
   * @param string[] $processingWarnings
   */
  public function setProcessingWarnings($processingWarnings)
  {
    $this->processingWarnings = $processingWarnings;
  }
  /**
   * @return string[]
   */
  public function getProcessingWarnings()
  {
    return $this->processingWarnings;
  }
  /**
   * A list of keyword tags that could be added to the video's metadata to
   * increase the likelihood that users will locate your video when searching or
   * browsing on YouTube.
   *
   * @param VideoSuggestionsTagSuggestion[] $tagSuggestions
   */
  public function setTagSuggestions($tagSuggestions)
  {
    $this->tagSuggestions = $tagSuggestions;
  }
  /**
   * @return VideoSuggestionsTagSuggestion[]
   */
  public function getTagSuggestions()
  {
    return $this->tagSuggestions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoSuggestions::class, 'Google_Service_YouTube_VideoSuggestions');
