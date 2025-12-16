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

class GoogleCloudAiplatformV1Segment extends \Google\Model
{
  /**
   * Output only. The end index of the segment in the `Part`, measured in bytes.
   * This marks the end of the segment and is exclusive, meaning the segment
   * includes content up to, but not including, the byte at this index.
   *
   * @var int
   */
  public $endIndex;
  /**
   * Output only. The index of the `Part` object that this segment belongs to.
   * This is useful for associating the segment with a specific part of the
   * content.
   *
   * @var int
   */
  public $partIndex;
  /**
   * Output only. The start index of the segment in the `Part`, measured in
   * bytes. This marks the beginning of the segment and is inclusive, meaning
   * the byte at this index is the first byte of the segment.
   *
   * @var int
   */
  public $startIndex;
  /**
   * Output only. The text of the segment.
   *
   * @var string
   */
  public $text;

  /**
   * Output only. The end index of the segment in the `Part`, measured in bytes.
   * This marks the end of the segment and is exclusive, meaning the segment
   * includes content up to, but not including, the byte at this index.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * Output only. The index of the `Part` object that this segment belongs to.
   * This is useful for associating the segment with a specific part of the
   * content.
   *
   * @param int $partIndex
   */
  public function setPartIndex($partIndex)
  {
    $this->partIndex = $partIndex;
  }
  /**
   * @return int
   */
  public function getPartIndex()
  {
    return $this->partIndex;
  }
  /**
   * Output only. The start index of the segment in the `Part`, measured in
   * bytes. This marks the beginning of the segment and is inclusive, meaning
   * the byte at this index is the first byte of the segment.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * Output only. The text of the segment.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Segment::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Segment');
