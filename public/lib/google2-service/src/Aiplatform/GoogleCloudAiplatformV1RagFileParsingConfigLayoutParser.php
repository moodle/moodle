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

class GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser extends \Google\Model
{
  /**
   * The maximum number of requests the job is allowed to make to the Document
   * AI processor per minute. Consult https://cloud.google.com/document-
   * ai/quotas and the Quota page for your project to set an appropriate value
   * here. If unspecified, a default value of 120 QPM would be used.
   *
   * @var int
   */
  public $maxParsingRequestsPerMin;
  /**
   * The full resource name of a Document AI processor or processor version. The
   * processor must have type `LAYOUT_PARSER_PROCESSOR`. If specified, the
   * `additional_config.parse_as_scanned_pdf` field must be false. Format: *
   * `projects/{project_id}/locations/{location}/processors/{processor_id}` * `p
   * rojects/{project_id}/locations/{location}/processors/{processor_id}/process
   * orVersions/{processor_version_id}`
   *
   * @var string
   */
  public $processorName;

  /**
   * The maximum number of requests the job is allowed to make to the Document
   * AI processor per minute. Consult https://cloud.google.com/document-
   * ai/quotas and the Quota page for your project to set an appropriate value
   * here. If unspecified, a default value of 120 QPM would be used.
   *
   * @param int $maxParsingRequestsPerMin
   */
  public function setMaxParsingRequestsPerMin($maxParsingRequestsPerMin)
  {
    $this->maxParsingRequestsPerMin = $maxParsingRequestsPerMin;
  }
  /**
   * @return int
   */
  public function getMaxParsingRequestsPerMin()
  {
    return $this->maxParsingRequestsPerMin;
  }
  /**
   * The full resource name of a Document AI processor or processor version. The
   * processor must have type `LAYOUT_PARSER_PROCESSOR`. If specified, the
   * `additional_config.parse_as_scanned_pdf` field must be false. Format: *
   * `projects/{project_id}/locations/{location}/processors/{processor_id}` * `p
   * rojects/{project_id}/locations/{location}/processors/{processor_id}/process
   * orVersions/{processor_version_id}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser');
