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

namespace Google\Service\AndroidPublisher;

class SafetyLabelsUpdateRequest extends \Google\Model
{
  /**
   * Required. Contents of the CSV file containing Data Safety responses. For
   * the format of this file, see the Help Center documentation at
   * https://support.google.com/googleplay/android-
   * developer/answer/10787469?#zippy=%2Cunderstand-the-csv-format To download
   * an up to date template, follow the steps at
   * https://support.google.com/googleplay/android-
   * developer/answer/10787469?#zippy=%2Cexport-to-a-csv-file
   *
   * @var string
   */
  public $safetyLabels;

  /**
   * Required. Contents of the CSV file containing Data Safety responses. For
   * the format of this file, see the Help Center documentation at
   * https://support.google.com/googleplay/android-
   * developer/answer/10787469?#zippy=%2Cunderstand-the-csv-format To download
   * an up to date template, follow the steps at
   * https://support.google.com/googleplay/android-
   * developer/answer/10787469?#zippy=%2Cexport-to-a-csv-file
   *
   * @param string $safetyLabels
   */
  public function setSafetyLabels($safetyLabels)
  {
    $this->safetyLabels = $safetyLabels;
  }
  /**
   * @return string
   */
  public function getSafetyLabels()
  {
    return $this->safetyLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SafetyLabelsUpdateRequest::class, 'Google_Service_AndroidPublisher_SafetyLabelsUpdateRequest');
