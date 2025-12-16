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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTextSegment extends \Google\Model
{
  /**
   * The text content in the segment for output only.
   *
   * @var string
   */
  public $content;
  /**
   * Zero-based character index of the first character past the end of the text
   * segment (counting character from the beginning of the text). The character
   * at the end_offset is NOT included in the text segment.
   *
   * @var string
   */
  public $endOffset;
  /**
   * Zero-based character index of the first character of the text segment
   * (counting characters from the beginning of the text).
   *
   * @var string
   */
  public $startOffset;

  /**
   * The text content in the segment for output only.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Zero-based character index of the first character past the end of the text
   * segment (counting character from the beginning of the text). The character
   * at the end_offset is NOT included in the text segment.
   *
   * @param string $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * Zero-based character index of the first character of the text segment
   * (counting characters from the beginning of the text).
   *
   * @param string $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTextSegment::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTextSegment');
