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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1TimestampValue extends \Google\Model
{
  /**
   * The string must represent a valid instant in UTC and is parsed using
   * java.time.format.DateTimeFormatter.ISO_INSTANT. e.g. "2013-09-29T18:46:19Z"
   *
   * @var string
   */
  public $textValue;
  /**
   * Timestamp value
   *
   * @var string
   */
  public $timestampValue;

  /**
   * The string must represent a valid instant in UTC and is parsed using
   * java.time.format.DateTimeFormatter.ISO_INSTANT. e.g. "2013-09-29T18:46:19Z"
   *
   * @param string $textValue
   */
  public function setTextValue($textValue)
  {
    $this->textValue = $textValue;
  }
  /**
   * @return string
   */
  public function getTextValue()
  {
    return $this->textValue;
  }
  /**
   * Timestamp value
   *
   * @param string $timestampValue
   */
  public function setTimestampValue($timestampValue)
  {
    $this->timestampValue = $timestampValue;
  }
  /**
   * @return string
   */
  public function getTimestampValue()
  {
    return $this->timestampValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1TimestampValue::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1TimestampValue');
