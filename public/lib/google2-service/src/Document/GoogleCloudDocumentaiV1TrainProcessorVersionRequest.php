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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1TrainProcessorVersionRequest extends \Google\Model
{
  /**
   * Optional. The processor version to use as a base for training. This
   * processor version must be a child of `parent`. Format: `projects/{project}/
   * locations/{location}/processors/{processor}/processorVersions/{processorVer
   * sion}`.
   *
   * @var string
   */
  public $baseProcessorVersion;
  protected $customDocumentExtractionOptionsType = GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions::class;
  protected $customDocumentExtractionOptionsDataType = '';
  protected $documentSchemaType = GoogleCloudDocumentaiV1DocumentSchema::class;
  protected $documentSchemaDataType = '';
  protected $foundationModelTuningOptionsType = GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions::class;
  protected $foundationModelTuningOptionsDataType = '';
  protected $inputDataType = GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData::class;
  protected $inputDataDataType = '';
  protected $processorVersionType = GoogleCloudDocumentaiV1ProcessorVersion::class;
  protected $processorVersionDataType = '';

  /**
   * Optional. The processor version to use as a base for training. This
   * processor version must be a child of `parent`. Format: `projects/{project}/
   * locations/{location}/processors/{processor}/processorVersions/{processorVer
   * sion}`.
   *
   * @param string $baseProcessorVersion
   */
  public function setBaseProcessorVersion($baseProcessorVersion)
  {
    $this->baseProcessorVersion = $baseProcessorVersion;
  }
  /**
   * @return string
   */
  public function getBaseProcessorVersion()
  {
    return $this->baseProcessorVersion;
  }
  /**
   * Options to control Custom Document Extraction (CDE) Processor.
   *
   * @param GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions $customDocumentExtractionOptions
   */
  public function setCustomDocumentExtractionOptions(GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions $customDocumentExtractionOptions)
  {
    $this->customDocumentExtractionOptions = $customDocumentExtractionOptions;
  }
  /**
   * @return GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions
   */
  public function getCustomDocumentExtractionOptions()
  {
    return $this->customDocumentExtractionOptions;
  }
  /**
   * Optional. The schema the processor version will be trained with.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchema $documentSchema
   */
  public function setDocumentSchema(GoogleCloudDocumentaiV1DocumentSchema $documentSchema)
  {
    $this->documentSchema = $documentSchema;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchema
   */
  public function getDocumentSchema()
  {
    return $this->documentSchema;
  }
  /**
   * Options to control foundation model tuning of a processor.
   *
   * @param GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions $foundationModelTuningOptions
   */
  public function setFoundationModelTuningOptions(GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions $foundationModelTuningOptions)
  {
    $this->foundationModelTuningOptions = $foundationModelTuningOptions;
  }
  /**
   * @return GoogleCloudDocumentaiV1TrainProcessorVersionRequestFoundationModelTuningOptions
   */
  public function getFoundationModelTuningOptions()
  {
    return $this->foundationModelTuningOptions;
  }
  /**
   * Optional. The input data used to train the ProcessorVersion.
   *
   * @param GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData $inputData
   */
  public function setInputData(GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData $inputData)
  {
    $this->inputData = $inputData;
  }
  /**
   * @return GoogleCloudDocumentaiV1TrainProcessorVersionRequestInputData
   */
  public function getInputData()
  {
    return $this->inputData;
  }
  /**
   * Required. The processor version to be created.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersion $processorVersion
   */
  public function setProcessorVersion(GoogleCloudDocumentaiV1ProcessorVersion $processorVersion)
  {
    $this->processorVersion = $processorVersion;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersion
   */
  public function getProcessorVersion()
  {
    return $this->processorVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1TrainProcessorVersionRequest::class, 'Google_Service_Document_GoogleCloudDocumentaiV1TrainProcessorVersionRequest');
