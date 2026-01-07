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

class GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec extends \Google\Model
{
  /**
   * Required. ID of the Feature to import values of. This Feature must exist in
   * the target EntityType, or the request will fail.
   *
   * @var string
   */
  public $id;
  /**
   * Source column to get the Feature values from. If not set, uses the column
   * with the same name as the Feature ID.
   *
   * @var string
   */
  public $sourceField;

  /**
   * Required. ID of the Feature to import values of. This Feature must exist in
   * the target EntityType, or the request will fail.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Source column to get the Feature values from. If not set, uses the column
   * with the same name as the Feature ID.
   *
   * @param string $sourceField
   */
  public function setSourceField($sourceField)
  {
    $this->sourceField = $sourceField;
  }
  /**
   * @return string
   */
  public function getSourceField()
  {
    return $this->sourceField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec');
