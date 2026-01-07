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

class GoogleCloudAiplatformV1PredefinedSplit extends \Google\Model
{
  /**
   * Required. The key is a name of one of the Dataset's data columns. The value
   * of the key (either the label's value or value in the column) must be one of
   * {`training`, `validation`, `test`}, and it defines to which set the given
   * piece of data is assigned. If for a piece of data the key is not present or
   * has an invalid value, that piece is ignored by the pipeline.
   *
   * @var string
   */
  public $key;

  /**
   * Required. The key is a name of one of the Dataset's data columns. The value
   * of the key (either the label's value or value in the column) must be one of
   * {`training`, `validation`, `test`}, and it defines to which set the given
   * piece of data is assigned. If for a piece of data the key is not present or
   * has an invalid value, that piece is ignored by the pipeline.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PredefinedSplit::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PredefinedSplit');
