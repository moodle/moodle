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

class DicomConfig extends \Google\Model
{
  /**
   * No tag filtration profile provided. Same as KEEP_ALL_PROFILE.
   */
  public const FILTER_PROFILE_TAG_FILTER_PROFILE_UNSPECIFIED = 'TAG_FILTER_PROFILE_UNSPECIFIED';
  /**
   * Keep only tags required to produce valid DICOM.
   */
  public const FILTER_PROFILE_MINIMAL_KEEP_LIST_PROFILE = 'MINIMAL_KEEP_LIST_PROFILE';
  /**
   * Remove tags based on DICOM Standard's Attribute Confidentiality Basic
   * Profile (DICOM Standard Edition 2018e) https://dicom.nema.org/medical/dicom
   * /2018e/output/chtml/part15/chapter_E.html.
   */
  public const FILTER_PROFILE_ATTRIBUTE_CONFIDENTIALITY_BASIC_PROFILE = 'ATTRIBUTE_CONFIDENTIALITY_BASIC_PROFILE';
  /**
   * Keep all tags.
   */
  public const FILTER_PROFILE_KEEP_ALL_PROFILE = 'KEEP_ALL_PROFILE';
  /**
   * Inspects within tag contents and replaces sensitive text. The process can
   * be configured using the TextConfig. Applies to all tags with the following
   * Value Representation names: AE, LO, LT, PN, SH, ST, UC, UT, DA, DT, AS
   */
  public const FILTER_PROFILE_DEIDENTIFY_TAG_CONTENTS = 'DEIDENTIFY_TAG_CONTENTS';
  /**
   * Tag filtering profile that determines which tags to keep/remove.
   *
   * @var string
   */
  public $filterProfile;
  protected $keepListType = TagFilterList::class;
  protected $keepListDataType = '';
  protected $removeListType = TagFilterList::class;
  protected $removeListDataType = '';
  /**
   * Optional. If true, skip replacing StudyInstanceUID, SeriesInstanceUID,
   * SOPInstanceUID, and MediaStorageSOPInstanceUID and leave them untouched.
   * The Cloud Healthcare API regenerates these UIDs by default based on the
   * DICOM Standard's reasoning: "Whilst these UIDs cannot be mapped directly to
   * an individual out of context, given access to the original images, or to a
   * database of the original images containing the UIDs, it would be possible
   * to recover the individual's identity." https://dicom.nema.org/medical/dicom
   * /current/output/chtml/part15/sect_E.3.9.html
   *
   * @var bool
   */
  public $skipIdRedaction;

  /**
   * Tag filtering profile that determines which tags to keep/remove.
   *
   * Accepted values: TAG_FILTER_PROFILE_UNSPECIFIED, MINIMAL_KEEP_LIST_PROFILE,
   * ATTRIBUTE_CONFIDENTIALITY_BASIC_PROFILE, KEEP_ALL_PROFILE,
   * DEIDENTIFY_TAG_CONTENTS
   *
   * @param self::FILTER_PROFILE_* $filterProfile
   */
  public function setFilterProfile($filterProfile)
  {
    $this->filterProfile = $filterProfile;
  }
  /**
   * @return self::FILTER_PROFILE_*
   */
  public function getFilterProfile()
  {
    return $this->filterProfile;
  }
  /**
   * List of tags to keep. Remove all other tags.
   *
   * @param TagFilterList $keepList
   */
  public function setKeepList(TagFilterList $keepList)
  {
    $this->keepList = $keepList;
  }
  /**
   * @return TagFilterList
   */
  public function getKeepList()
  {
    return $this->keepList;
  }
  /**
   * List of tags to remove. Keep all other tags.
   *
   * @param TagFilterList $removeList
   */
  public function setRemoveList(TagFilterList $removeList)
  {
    $this->removeList = $removeList;
  }
  /**
   * @return TagFilterList
   */
  public function getRemoveList()
  {
    return $this->removeList;
  }
  /**
   * Optional. If true, skip replacing StudyInstanceUID, SeriesInstanceUID,
   * SOPInstanceUID, and MediaStorageSOPInstanceUID and leave them untouched.
   * The Cloud Healthcare API regenerates these UIDs by default based on the
   * DICOM Standard's reasoning: "Whilst these UIDs cannot be mapped directly to
   * an individual out of context, given access to the original images, or to a
   * database of the original images containing the UIDs, it would be possible
   * to recover the individual's identity." https://dicom.nema.org/medical/dicom
   * /current/output/chtml/part15/sect_E.3.9.html
   *
   * @param bool $skipIdRedaction
   */
  public function setSkipIdRedaction($skipIdRedaction)
  {
    $this->skipIdRedaction = $skipIdRedaction;
  }
  /**
   * @return bool
   */
  public function getSkipIdRedaction()
  {
    return $this->skipIdRedaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DicomConfig::class, 'Google_Service_CloudHealthcare_DicomConfig');
