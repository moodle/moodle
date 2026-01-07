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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p1beta1TextAnnotationDetectedBreak extends \Google\Model
{
  /**
   * Unknown break label type.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Regular space.
   */
  public const TYPE_SPACE = 'SPACE';
  /**
   * Sure space (very wide).
   */
  public const TYPE_SURE_SPACE = 'SURE_SPACE';
  /**
   * Line-wrapping break.
   */
  public const TYPE_EOL_SURE_SPACE = 'EOL_SURE_SPACE';
  /**
   * End-line hyphen that is not present in text; does not co-occur with
   * `SPACE`, `LEADER_SPACE`, or `LINE_BREAK`.
   */
  public const TYPE_HYPHEN = 'HYPHEN';
  /**
   * Line break that ends a paragraph.
   */
  public const TYPE_LINE_BREAK = 'LINE_BREAK';
  /**
   * True if break prepends the element.
   *
   * @var bool
   */
  public $isPrefix;
  /**
   * Detected break type.
   *
   * @var string
   */
  public $type;

  /**
   * True if break prepends the element.
   *
   * @param bool $isPrefix
   */
  public function setIsPrefix($isPrefix)
  {
    $this->isPrefix = $isPrefix;
  }
  /**
   * @return bool
   */
  public function getIsPrefix()
  {
    return $this->isPrefix;
  }
  /**
   * Detected break type.
   *
   * Accepted values: UNKNOWN, SPACE, SURE_SPACE, EOL_SURE_SPACE, HYPHEN,
   * LINE_BREAK
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p1beta1TextAnnotationDetectedBreak::class, 'Google_Service_Vision_GoogleCloudVisionV1p1beta1TextAnnotationDetectedBreak');
