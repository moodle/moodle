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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Deidentify extends \Google\Collection
{
  protected $collection_key = 'fileTypesToTransform';
  /**
   * Required. User settable Cloud Storage bucket and folders to store de-
   * identified files. This field must be set for Cloud Storage
   * deidentification. The output Cloud Storage bucket must be different from
   * the input bucket. De-identified files will overwrite files in the output
   * path. Form of: gs://bucket/folder/ or gs://bucket
   *
   * @var string
   */
  public $cloudStorageOutput;
  /**
   * List of user-specified file type groups to transform. If specified, only
   * the files with these file types are transformed. If empty, all supported
   * files are transformed. Supported types may be automatically added over
   * time. Any unsupported file types that are set in this field are excluded
   * from de-identification. An error is recorded for each unsupported file in
   * the TransformationDetails output table. Currently the only file types
   * supported are: IMAGES, TEXT_FILES, CSV, TSV.
   *
   * @var string[]
   */
  public $fileTypesToTransform;
  protected $transformationConfigType = GooglePrivacyDlpV2TransformationConfig::class;
  protected $transformationConfigDataType = '';
  protected $transformationDetailsStorageConfigType = GooglePrivacyDlpV2TransformationDetailsStorageConfig::class;
  protected $transformationDetailsStorageConfigDataType = '';

  /**
   * Required. User settable Cloud Storage bucket and folders to store de-
   * identified files. This field must be set for Cloud Storage
   * deidentification. The output Cloud Storage bucket must be different from
   * the input bucket. De-identified files will overwrite files in the output
   * path. Form of: gs://bucket/folder/ or gs://bucket
   *
   * @param string $cloudStorageOutput
   */
  public function setCloudStorageOutput($cloudStorageOutput)
  {
    $this->cloudStorageOutput = $cloudStorageOutput;
  }
  /**
   * @return string
   */
  public function getCloudStorageOutput()
  {
    return $this->cloudStorageOutput;
  }
  /**
   * List of user-specified file type groups to transform. If specified, only
   * the files with these file types are transformed. If empty, all supported
   * files are transformed. Supported types may be automatically added over
   * time. Any unsupported file types that are set in this field are excluded
   * from de-identification. An error is recorded for each unsupported file in
   * the TransformationDetails output table. Currently the only file types
   * supported are: IMAGES, TEXT_FILES, CSV, TSV.
   *
   * @param string[] $fileTypesToTransform
   */
  public function setFileTypesToTransform($fileTypesToTransform)
  {
    $this->fileTypesToTransform = $fileTypesToTransform;
  }
  /**
   * @return string[]
   */
  public function getFileTypesToTransform()
  {
    return $this->fileTypesToTransform;
  }
  /**
   * User specified deidentify templates and configs for structured,
   * unstructured, and image files.
   *
   * @param GooglePrivacyDlpV2TransformationConfig $transformationConfig
   */
  public function setTransformationConfig(GooglePrivacyDlpV2TransformationConfig $transformationConfig)
  {
    $this->transformationConfig = $transformationConfig;
  }
  /**
   * @return GooglePrivacyDlpV2TransformationConfig
   */
  public function getTransformationConfig()
  {
    return $this->transformationConfig;
  }
  /**
   * Config for storing transformation details. This field specifies the
   * configuration for storing detailed metadata about each transformation
   * performed during a de-identification process. The metadata is stored
   * separately from the de-identified content itself and provides a granular
   * record of both successful transformations and any failures that occurred.
   * Enabling this configuration is essential for users who need to access
   * comprehensive information about the status, outcome, and specifics of each
   * transformation. The details are captured in the TransformationDetails
   * message for each operation. Key use cases: * **Auditing and compliance** *
   * Provides a verifiable audit trail of de-identification activities, which is
   * crucial for meeting regulatory requirements and internal data governance
   * policies. * Logs what data was transformed, what transformations were
   * applied, when they occurred, and their success status. This helps
   * demonstrate accountability and due diligence in protecting sensitive data.
   * * **Troubleshooting and debugging** * Offers detailed error messages and
   * context if a transformation fails. This information is useful for
   * diagnosing and resolving issues in the de-identification pipeline. * Helps
   * pinpoint the exact location and nature of failures, speeding up the
   * debugging process. * **Process verification and quality assurance** *
   * Allows users to confirm that de-identification rules and transformations
   * were applied correctly and consistently across the dataset as intended. *
   * Helps in verifying the effectiveness of the chosen de-identification
   * strategies. * **Data lineage and impact analysis** * Creates a record of
   * how data elements were modified, contributing to data lineage. This is
   * useful for understanding the provenance of de-identified data. * Aids in
   * assessing the potential impact of de-identification choices on downstream
   * analytical processes or data usability. * **Reporting and operational
   * insights** * You can analyze the metadata stored in a queryable BigQuery
   * table to generate reports on transformation success rates, common error
   * types, processing volumes (e.g., transformedBytes), and the types of
   * transformations applied. * These insights can inform optimization of de-
   * identification configurations and resource planning. To take advantage of
   * these benefits, set this configuration. The stored details include a
   * description of the transformation, success or error codes, error messages,
   * the number of bytes transformed, the location of the transformed content,
   * and identifiers for the job and source data.
   *
   * @param GooglePrivacyDlpV2TransformationDetailsStorageConfig $transformationDetailsStorageConfig
   */
  public function setTransformationDetailsStorageConfig(GooglePrivacyDlpV2TransformationDetailsStorageConfig $transformationDetailsStorageConfig)
  {
    $this->transformationDetailsStorageConfig = $transformationDetailsStorageConfig;
  }
  /**
   * @return GooglePrivacyDlpV2TransformationDetailsStorageConfig
   */
  public function getTransformationDetailsStorageConfig()
  {
    return $this->transformationDetailsStorageConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Deidentify::class, 'Google_Service_DLP_GooglePrivacyDlpV2Deidentify');
