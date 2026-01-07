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

namespace Google\Service\CloudHealthcare;

class TagFilterList extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Optional. Tags to be filtered. Tags must be DICOM Data Elements, File Meta
   * Elements, or Directory Structuring Elements, as defined at: https://dicom.n
   * ema.org/medical/dicom/current/output/html/part06.html#table_6-1,. They may
   * be provided by "Keyword" or "Tag". For example "PatientID", "00100010".
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. Tags to be filtered. Tags must be DICOM Data Elements, File Meta
   * Elements, or Directory Structuring Elements, as defined at: https://dicom.n
   * ema.org/medical/dicom/current/output/html/part06.html#table_6-1,. They may
   * be provided by "Keyword" or "Tag". For example "PatientID", "00100010".
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagFilterList::class, 'Google_Service_CloudHealthcare_TagFilterList');
