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

class GoogleCloudContentwarehouseV1IngestPipelineConfig extends \Google\Model
{
  /**
   * The Cloud Function resource name. The Cloud Function needs to live inside
   * consumer project and is accessible to Document AI Warehouse P4SA. Only
   * Cloud Functions V2 is supported. Cloud function execution should complete
   * within 5 minutes or this file ingestion may fail due to timeout. Format:
   * `https://{region}-{project_id}.cloudfunctions.net/{cloud_function}` The
   * following keys are available the request json payload. * display_name *
   * properties * plain_text * reference_id * document_schema_name *
   * raw_document_path * raw_document_file_type The following keys from the
   * cloud function json response payload will be ingested to the Document AI
   * Warehouse as part of Document proto content and/or related information. The
   * original values will be overridden if any key is present in the response. *
   * display_name * properties * plain_text * document_acl_policy * folder
   *
   * @var string
   */
  public $cloudFunction;
  protected $documentAclPolicyType = GoogleIamV1Policy::class;
  protected $documentAclPolicyDataType = '';
  /**
   * The document text extraction enabled flag. If the flag is set to true, DWH
   * will perform text extraction on the raw document.
   *
   * @var bool
   */
  public $enableDocumentTextExtraction;
  /**
   * Optional. The name of the folder to which all ingested documents will be
   * linked during ingestion process. Format is
   * `projects/{project}/locations/{location}/documents/{folder_id}`
   *
   * @var string
   */
  public $folder;

  /**
   * The Cloud Function resource name. The Cloud Function needs to live inside
   * consumer project and is accessible to Document AI Warehouse P4SA. Only
   * Cloud Functions V2 is supported. Cloud function execution should complete
   * within 5 minutes or this file ingestion may fail due to timeout. Format:
   * `https://{region}-{project_id}.cloudfunctions.net/{cloud_function}` The
   * following keys are available the request json payload. * display_name *
   * properties * plain_text * reference_id * document_schema_name *
   * raw_document_path * raw_document_file_type The following keys from the
   * cloud function json response payload will be ingested to the Document AI
   * Warehouse as part of Document proto content and/or related information. The
   * original values will be overridden if any key is present in the response. *
   * display_name * properties * plain_text * document_acl_policy * folder
   *
   * @param string $cloudFunction
   */
  public function setCloudFunction($cloudFunction)
  {
    $this->cloudFunction = $cloudFunction;
  }
  /**
   * @return string
   */
  public function getCloudFunction()
  {
    return $this->cloudFunction;
  }
  /**
   * The document level acl policy config. This refers to an Identity and Access
   * (IAM) policy, which specifies access controls for all documents ingested by
   * the pipeline. The role and members under the policy needs to be specified.
   * The following roles are supported for document level acl control: *
   * roles/contentwarehouse.documentAdmin *
   * roles/contentwarehouse.documentEditor *
   * roles/contentwarehouse.documentViewer The following members are supported
   * for document level acl control: * user:user-email@example.com *
   * group:group-email@example.com Note that for documents searched with LLM,
   * only single level user or group acl check is supported.
   *
   * @param GoogleIamV1Policy $documentAclPolicy
   */
  public function setDocumentAclPolicy(GoogleIamV1Policy $documentAclPolicy)
  {
    $this->documentAclPolicy = $documentAclPolicy;
  }
  /**
   * @return GoogleIamV1Policy
   */
  public function getDocumentAclPolicy()
  {
    return $this->documentAclPolicy;
  }
  /**
   * The document text extraction enabled flag. If the flag is set to true, DWH
   * will perform text extraction on the raw document.
   *
   * @param bool $enableDocumentTextExtraction
   */
  public function setEnableDocumentTextExtraction($enableDocumentTextExtraction)
  {
    $this->enableDocumentTextExtraction = $enableDocumentTextExtraction;
  }
  /**
   * @return bool
   */
  public function getEnableDocumentTextExtraction()
  {
    return $this->enableDocumentTextExtraction;
  }
  /**
   * Optional. The name of the folder to which all ingested documents will be
   * linked during ingestion process. Format is
   * `projects/{project}/locations/{location}/documents/{folder_id}`
   *
   * @param string $folder
   */
  public function setFolder($folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return string
   */
  public function getFolder()
  {
    return $this->folder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1IngestPipelineConfig::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1IngestPipelineConfig');
