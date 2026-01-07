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

class GoogleCloudAiplatformV1FeatureValueList extends \Google\Collection
{
  protected $collection_key = 'values';
  protected $valuesType = GoogleCloudAiplatformV1FeatureValue::class;
  protected $valuesDataType = 'array';

  /**
   * A list of feature values. All of them should be the same data type.
   *
   * @param GoogleCloudAiplatformV1FeatureValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureValueList::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureValueList');
