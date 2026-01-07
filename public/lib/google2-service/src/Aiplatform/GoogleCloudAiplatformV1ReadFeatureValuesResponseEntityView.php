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

class GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView extends \Google\Collection
{
  protected $collection_key = 'data';
  protected $dataType = GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityViewData::class;
  protected $dataDataType = 'array';
  /**
   * ID of the requested entity.
   *
   * @var string
   */
  public $entityId;

  /**
   * Each piece of data holds the k requested values for one requested Feature.
   * If no values for the requested Feature exist, the corresponding cell will
   * be empty. This has the same size and is in the same order as the features
   * from the header ReadFeatureValuesResponse.header.
   *
   * @param GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityViewData[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityViewData[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * ID of the requested entity.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView');
