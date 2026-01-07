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

class GoogleCloudAiplatformV1UnmanagedContainerModel extends \Google\Model
{
  /**
   * The path to the directory containing the Model artifact and any of its
   * supporting files.
   *
   * @var string
   */
  public $artifactUri;
  protected $containerSpecType = GoogleCloudAiplatformV1ModelContainerSpec::class;
  protected $containerSpecDataType = '';
  protected $predictSchemataType = GoogleCloudAiplatformV1PredictSchemata::class;
  protected $predictSchemataDataType = '';

  /**
   * The path to the directory containing the Model artifact and any of its
   * supporting files.
   *
   * @param string $artifactUri
   */
  public function setArtifactUri($artifactUri)
  {
    $this->artifactUri = $artifactUri;
  }
  /**
   * @return string
   */
  public function getArtifactUri()
  {
    return $this->artifactUri;
  }
  /**
   * Input only. The specification of the container that is to be used when
   * deploying this Model.
   *
   * @param GoogleCloudAiplatformV1ModelContainerSpec $containerSpec
   */
  public function setContainerSpec(GoogleCloudAiplatformV1ModelContainerSpec $containerSpec)
  {
    $this->containerSpec = $containerSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelContainerSpec
   */
  public function getContainerSpec()
  {
    return $this->containerSpec;
  }
  /**
   * Contains the schemata used in Model's predictions and explanations
   *
   * @param GoogleCloudAiplatformV1PredictSchemata $predictSchemata
   */
  public function setPredictSchemata(GoogleCloudAiplatformV1PredictSchemata $predictSchemata)
  {
    $this->predictSchemata = $predictSchemata;
  }
  /**
   * @return GoogleCloudAiplatformV1PredictSchemata
   */
  public function getPredictSchemata()
  {
    return $this->predictSchemata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UnmanagedContainerModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UnmanagedContainerModel');
