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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTables extends \Google\Model
{
  protected $inputsType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs::class;
  protected $inputsDataType = '';
  protected $metadataType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesMetadata::class;
  protected $metadataDataType = '';

  /**
   * The input parameters of this TrainingJob.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs $inputs
   */
  public function setInputs(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs $inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * The metadata information.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesMetadata $metadata
   */
  public function setMetadata(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTables::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTables');
