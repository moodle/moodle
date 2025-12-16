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

class GoogleCloudVisionV1p1beta1WebDetectionWebLabel extends \Google\Model
{
  /**
   * Label for extra metadata.
   *
   * @var string
   */
  public $label;
  /**
   * The BCP-47 language code for `label`, such as "en-US" or "sr-Latn". For
   * more information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Label for extra metadata.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The BCP-47 language code for `label`, such as "en-US" or "sr-Latn". For
   * more information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p1beta1WebDetectionWebLabel::class, 'Google_Service_Vision_GoogleCloudVisionV1p1beta1WebDetectionWebLabel');
