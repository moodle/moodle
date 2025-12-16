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

class GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions extends \Google\Model
{
  /**
   * Optional. Whether to disable the inference of data type for Json data. If
   * true, all columns will be registered as their primitive types (strings,
   * number or boolean).
   *
   * @var bool
   */
  public $disableTypeInference;
  /**
   * Optional. The character encoding of the data. The default is UTF-8.
   *
   * @var string
   */
  public $encoding;

  /**
   * Optional. Whether to disable the inference of data type for Json data. If
   * true, all columns will be registered as their primitive types (strings,
   * number or boolean).
   *
   * @param bool $disableTypeInference
   */
  public function setDisableTypeInference($disableTypeInference)
  {
    $this->disableTypeInference = $disableTypeInference;
  }
  /**
   * @return bool
   */
  public function getDisableTypeInference()
  {
    return $this->disableTypeInference;
  }
  /**
   * Optional. The character encoding of the data. The default is UTF-8.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions');
