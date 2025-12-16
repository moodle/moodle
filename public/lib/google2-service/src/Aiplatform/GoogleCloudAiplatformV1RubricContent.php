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

class GoogleCloudAiplatformV1RubricContent extends \Google\Model
{
  protected $propertyType = GoogleCloudAiplatformV1RubricContentProperty::class;
  protected $propertyDataType = '';

  /**
   * Evaluation criteria based on a specific property.
   *
   * @param GoogleCloudAiplatformV1RubricContentProperty $property
   */
  public function setProperty(GoogleCloudAiplatformV1RubricContentProperty $property)
  {
    $this->property = $property;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricContentProperty
   */
  public function getProperty()
  {
    return $this->property;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricContent::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricContent');
