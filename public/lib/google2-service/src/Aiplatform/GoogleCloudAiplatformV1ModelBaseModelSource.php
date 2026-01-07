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

class GoogleCloudAiplatformV1ModelBaseModelSource extends \Google\Model
{
  protected $genieSourceType = GoogleCloudAiplatformV1GenieSource::class;
  protected $genieSourceDataType = '';
  protected $modelGardenSourceType = GoogleCloudAiplatformV1ModelGardenSource::class;
  protected $modelGardenSourceDataType = '';

  /**
   * Information about the base model of Genie models.
   *
   * @param GoogleCloudAiplatformV1GenieSource $genieSource
   */
  public function setGenieSource(GoogleCloudAiplatformV1GenieSource $genieSource)
  {
    $this->genieSource = $genieSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GenieSource
   */
  public function getGenieSource()
  {
    return $this->genieSource;
  }
  /**
   * Source information of Model Garden models.
   *
   * @param GoogleCloudAiplatformV1ModelGardenSource $modelGardenSource
   */
  public function setModelGardenSource(GoogleCloudAiplatformV1ModelGardenSource $modelGardenSource)
  {
    $this->modelGardenSource = $modelGardenSource;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelGardenSource
   */
  public function getModelGardenSource()
  {
    return $this->modelGardenSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelBaseModelSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelBaseModelSource');
