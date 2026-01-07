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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo extends \Google\Model
{
  /**
   * Output only. Average length of non-null values in the scanned data.
   *
   * @var 
   */
  public $averageLength;
  /**
   * Output only. Maximum length of non-null values in the scanned data.
   *
   * @var string
   */
  public $maxLength;
  /**
   * Output only. Minimum length of non-null values in the scanned data.
   *
   * @var string
   */
  public $minLength;

  public function setAverageLength($averageLength)
  {
    $this->averageLength = $averageLength;
  }
  public function getAverageLength()
  {
    return $this->averageLength;
  }
  /**
   * Output only. Maximum length of non-null values in the scanned data.
   *
   * @param string $maxLength
   */
  public function setMaxLength($maxLength)
  {
    $this->maxLength = $maxLength;
  }
  /**
   * @return string
   */
  public function getMaxLength()
  {
    return $this->maxLength;
  }
  /**
   * Output only. Minimum length of non-null values in the scanned data.
   *
   * @param string $minLength
   */
  public function setMinLength($minLength)
  {
    $this->minLength = $minLength;
  }
  /**
   * @return string
   */
  public function getMinLength()
  {
    return $this->minLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfoStringFieldInfo');
