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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1ProcessorInfo extends \Google\Model
{
  /**
   * The processor will process the documents with this document type.
   *
   * @var string
   */
  public $documentType;
  /**
   * The processor resource name. Format is
   * `projects/{project}/locations/{location}/processors/{processor}`, or `proje
   * cts/{project}/locations/{location}/processors/{processor}/processorVersions
   * /{processorVersion}`
   *
   * @var string
   */
  public $processorName;
  /**
   * The Document schema resource name. All documents processed by this
   * processor will use this schema. Format: projects/{project_number}/locations
   * /{location}/documentSchemas/{document_schema_id}.
   *
   * @var string
   */
  public $schemaName;

  /**
   * The processor will process the documents with this document type.
   *
   * @param string $documentType
   */
  public function setDocumentType($documentType)
  {
    $this->documentType = $documentType;
  }
  /**
   * @return string
   */
  public function getDocumentType()
  {
    return $this->documentType;
  }
  /**
   * The processor resource name. Format is
   * `projects/{project}/locations/{location}/processors/{processor}`, or `proje
   * cts/{project}/locations/{location}/processors/{processor}/processorVersions
   * /{processorVersion}`
   *
   * @param string $processorName
   */
  public function setProcessorName($processorName)
  {
    $this->processorName = $processorName;
  }
  /**
   * @return string
   */
  public function getProcessorName()
  {
    return $this->processorName;
  }
  /**
   * The Document schema resource name. All documents processed by this
   * processor will use this schema. Format: projects/{project_number}/locations
   * /{location}/documentSchemas/{document_schema_id}.
   *
   * @param string $schemaName
   */
  public function setSchemaName($schemaName)
  {
    $this->schemaName = $schemaName;
  }
  /**
   * @return string
   */
  public function getSchemaName()
  {
    return $this->schemaName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1ProcessorInfo::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1ProcessorInfo');
