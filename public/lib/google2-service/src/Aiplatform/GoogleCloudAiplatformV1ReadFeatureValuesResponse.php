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

class GoogleCloudAiplatformV1ReadFeatureValuesResponse extends \Google\Model
{
  protected $entityViewType = GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView::class;
  protected $entityViewDataType = '';
  protected $headerType = GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader::class;
  protected $headerDataType = '';

  /**
   * Entity view with Feature values. This may be the entity in the Featurestore
   * if values for all Features were requested, or a projection of the entity in
   * the Featurestore if values for only some Features were requested.
   *
   * @param GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView $entityView
   */
  public function setEntityView(GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView $entityView)
  {
    $this->entityView = $entityView;
  }
  /**
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponseEntityView
   */
  public function getEntityView()
  {
    return $this->entityView;
  }
  /**
   * Response header.
   *
   * @param GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader $header
   */
  public function setHeader(GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader $header)
  {
    $this->header = $header;
  }
  /**
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader
   */
  public function getHeader()
  {
    return $this->header;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadFeatureValuesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadFeatureValuesResponse');
