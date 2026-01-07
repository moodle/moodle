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

class GoogleCloudAiplatformV1MetadataStoreDataplexConfig extends \Google\Model
{
  /**
   * Optional. Whether or not Data Lineage synchronization is enabled for Vertex
   * Pipelines.
   *
   * @var bool
   */
  public $enabledPipelinesLineage;

  /**
   * Optional. Whether or not Data Lineage synchronization is enabled for Vertex
   * Pipelines.
   *
   * @param bool $enabledPipelinesLineage
   */
  public function setEnabledPipelinesLineage($enabledPipelinesLineage)
  {
    $this->enabledPipelinesLineage = $enabledPipelinesLineage;
  }
  /**
   * @return bool
   */
  public function getEnabledPipelinesLineage()
  {
    return $this->enabledPipelinesLineage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MetadataStoreDataplexConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MetadataStoreDataplexConfig');
