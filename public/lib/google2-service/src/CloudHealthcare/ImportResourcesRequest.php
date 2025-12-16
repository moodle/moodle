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

namespace Google\Service\CloudHealthcare;

class ImportResourcesRequest extends \Google\Model
{
  /**
   * If the content structure is not specified, the default value `BUNDLE` is
   * used.
   */
  public const CONTENT_STRUCTURE_CONTENT_STRUCTURE_UNSPECIFIED = 'CONTENT_STRUCTURE_UNSPECIFIED';
  /**
   * The source file contains one or more lines of newline-delimited JSON
   * (ndjson). Each line is a bundle that contains one or more resources.
   */
  public const CONTENT_STRUCTURE_BUNDLE = 'BUNDLE';
  /**
   * The source file contains one or more lines of newline-delimited JSON
   * (ndjson). Each line is a single resource.
   */
  public const CONTENT_STRUCTURE_RESOURCE = 'RESOURCE';
  /**
   * The entire file is one JSON bundle. The JSON can span multiple lines.
   */
  public const CONTENT_STRUCTURE_BUNDLE_PRETTY = 'BUNDLE_PRETTY';
  /**
   * The entire file is one JSON resource. The JSON can span multiple lines.
   */
  public const CONTENT_STRUCTURE_RESOURCE_PRETTY = 'RESOURCE_PRETTY';
  /**
   * The content structure in the source location. If not specified, the server
   * treats the input source files as BUNDLE.
   *
   * @var string
   */
  public $contentStructure;
  protected $gcsSourceType = GoogleCloudHealthcareV1FhirGcsSource::class;
  protected $gcsSourceDataType = '';

  /**
   * The content structure in the source location. If not specified, the server
   * treats the input source files as BUNDLE.
   *
   * Accepted values: CONTENT_STRUCTURE_UNSPECIFIED, BUNDLE, RESOURCE,
   * BUNDLE_PRETTY, RESOURCE_PRETTY
   *
   * @param self::CONTENT_STRUCTURE_* $contentStructure
   */
  public function setContentStructure($contentStructure)
  {
    $this->contentStructure = $contentStructure;
  }
  /**
   * @return self::CONTENT_STRUCTURE_*
   */
  public function getContentStructure()
  {
    return $this->contentStructure;
  }
  /**
   * Cloud Storage source data location and import configuration. The Healthcare
   * Service Agent account requires the `roles/storage.objectAdmin` role on the
   * Cloud Storage location. Each Cloud Storage object should be a text file
   * that contains the format specified in ContentStructure.
   *
   * @param GoogleCloudHealthcareV1FhirGcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudHealthcareV1FhirGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudHealthcareV1FhirGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportResourcesRequest::class, 'Google_Service_CloudHealthcare_ImportResourcesRequest');
