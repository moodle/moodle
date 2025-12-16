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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1AnnotationSpec extends \Google\Model
{
  /**
   * Optional. User-provided description of the annotation specification. The
   * description can be up to 10,000 characters long.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the AnnotationSpec. Maximum of 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This is the integer index of the AnnotationSpec. The index for
   * the whole AnnotationSpecSet is sequential starting from 0. For example, an
   * AnnotationSpecSet with classes `dog` and `cat`, might contain one
   * AnnotationSpec with `{ display_name: "dog", index: 0 }` and one
   * AnnotationSpec with `{ display_name: "cat", index: 1 }`. This is especially
   * useful for model training as it encodes the string labels into numeric
   * values.
   *
   * @var int
   */
  public $index;

  /**
   * Optional. User-provided description of the annotation specification. The
   * description can be up to 10,000 characters long.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the AnnotationSpec. Maximum of 64 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. This is the integer index of the AnnotationSpec. The index for
   * the whole AnnotationSpecSet is sequential starting from 0. For example, an
   * AnnotationSpecSet with classes `dog` and `cat`, might contain one
   * AnnotationSpec with `{ display_name: "dog", index: 0 }` and one
   * AnnotationSpec with `{ display_name: "cat", index: 1 }`. This is especially
   * useful for model training as it encodes the string labels into numeric
   * values.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1AnnotationSpec::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1AnnotationSpec');
