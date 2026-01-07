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

class GoogleCloudAiplatformV1ExportModelRequestOutputConfig extends \Google\Model
{
  protected $artifactDestinationType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $artifactDestinationDataType = '';
  /**
   * The ID of the format in which the Model must be exported. Each Model lists
   * the export formats it supports. If no value is provided here, then the
   * first from the list of the Model's supported formats is used by default.
   *
   * @var string
   */
  public $exportFormatId;
  protected $imageDestinationType = GoogleCloudAiplatformV1ContainerRegistryDestination::class;
  protected $imageDestinationDataType = '';

  /**
   * The Cloud Storage location where the Model artifact is to be written to.
   * Under the directory given as the destination a new one with name "`model-
   * export--`", where timestamp is in YYYY-MM-DDThh:mm:ss.sssZ ISO-8601 format,
   * will be created. Inside, the Model and any of its supporting files will be
   * written. This field should only be set when the `exportableContent` field
   * of the [Model.supported_export_formats] object contains `ARTIFACT`.
   *
   * @param GoogleCloudAiplatformV1GcsDestination $artifactDestination
   */
  public function setArtifactDestination(GoogleCloudAiplatformV1GcsDestination $artifactDestination)
  {
    $this->artifactDestination = $artifactDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getArtifactDestination()
  {
    return $this->artifactDestination;
  }
  /**
   * The ID of the format in which the Model must be exported. Each Model lists
   * the export formats it supports. If no value is provided here, then the
   * first from the list of the Model's supported formats is used by default.
   *
   * @param string $exportFormatId
   */
  public function setExportFormatId($exportFormatId)
  {
    $this->exportFormatId = $exportFormatId;
  }
  /**
   * @return string
   */
  public function getExportFormatId()
  {
    return $this->exportFormatId;
  }
  /**
   * The Google Container Registry or Artifact Registry uri where the Model
   * container image will be copied to. This field should only be set when the
   * `exportableContent` field of the [Model.supported_export_formats] object
   * contains `IMAGE`.
   *
   * @param GoogleCloudAiplatformV1ContainerRegistryDestination $imageDestination
   */
  public function setImageDestination(GoogleCloudAiplatformV1ContainerRegistryDestination $imageDestination)
  {
    $this->imageDestination = $imageDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1ContainerRegistryDestination
   */
  public function getImageDestination()
  {
    return $this->imageDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportModelRequestOutputConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportModelRequestOutputConfig');
