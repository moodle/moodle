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

class GoogleCloudAiplatformV1DeleteFeatureValuesResponse extends \Google\Model
{
  protected $selectEntityType = GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity::class;
  protected $selectEntityDataType = '';
  protected $selectTimeRangeAndFeatureType = GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature::class;
  protected $selectTimeRangeAndFeatureDataType = '';

  /**
   * Response for request specifying the entities to delete
   *
   * @param GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity $selectEntity
   */
  public function setSelectEntity(GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity $selectEntity)
  {
    $this->selectEntity = $selectEntity;
  }
  /**
   * @return GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity
   */
  public function getSelectEntity()
  {
    return $this->selectEntity;
  }
  /**
   * Response for request specifying time range and feature
   *
   * @param GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature $selectTimeRangeAndFeature
   */
  public function setSelectTimeRangeAndFeature(GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature $selectTimeRangeAndFeature)
  {
    $this->selectTimeRangeAndFeature = $selectTimeRangeAndFeature;
  }
  /**
   * @return GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature
   */
  public function getSelectTimeRangeAndFeature()
  {
    return $this->selectTimeRangeAndFeature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeleteFeatureValuesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeleteFeatureValuesResponse');
