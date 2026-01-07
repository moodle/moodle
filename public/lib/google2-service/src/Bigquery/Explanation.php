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

namespace Google\Service\Bigquery;

class Explanation extends \Google\Model
{
  /**
   * Attribution of feature.
   *
   * @var 
   */
  public $attribution;
  /**
   * The full feature name. For non-numerical features, will be formatted like
   * `.`. Overall size of feature name will always be truncated to first 120
   * characters.
   *
   * @var string
   */
  public $featureName;

  public function setAttribution($attribution)
  {
    $this->attribution = $attribution;
  }
  public function getAttribution()
  {
    return $this->attribution;
  }
  /**
   * The full feature name. For non-numerical features, will be formatted like
   * `.`. Overall size of feature name will always be truncated to first 120
   * characters.
   *
   * @param string $featureName
   */
  public function setFeatureName($featureName)
  {
    $this->featureName = $featureName;
  }
  /**
   * @return string
   */
  public function getFeatureName()
  {
    return $this->featureName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Explanation::class, 'Google_Service_Bigquery_Explanation');
