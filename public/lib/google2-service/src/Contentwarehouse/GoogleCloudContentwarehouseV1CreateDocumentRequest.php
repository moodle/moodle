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

class GoogleCloudContentwarehouseV1CreateDocumentRequest extends \Google\Model
{
  protected $cloudAiDocumentOptionType = GoogleCloudContentwarehouseV1CloudAIDocumentOption::class;
  protected $cloudAiDocumentOptionDataType = '';
  /**
   * Field mask for creating Document fields. If mask path is empty, it means
   * all fields are masked. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask.
   *
   * @var string
   */
  public $createMask;
  protected $documentType = GoogleCloudContentwarehouseV1Document::class;
  protected $documentDataType = '';
  protected $policyType = GoogleIamV1Policy::class;
  protected $policyDataType = '';
  protected $requestMetadataType = GoogleCloudContentwarehouseV1RequestMetadata::class;
  protected $requestMetadataDataType = '';

  /**
   * Request Option for processing Cloud AI Document in Document Warehouse. This
   * field offers limited support for mapping entities from Cloud AI Document to
   * Warehouse Document. Please consult with product team before using this
   * field and other available options.
   *
   * @param GoogleCloudContentwarehouseV1CloudAIDocumentOption $cloudAiDocumentOption
   */
  public function setCloudAiDocumentOption(GoogleCloudContentwarehouseV1CloudAIDocumentOption $cloudAiDocumentOption)
  {
    $this->cloudAiDocumentOption = $cloudAiDocumentOption;
  }
  /**
   * @return GoogleCloudContentwarehouseV1CloudAIDocumentOption
   */
  public function getCloudAiDocumentOption()
  {
    return $this->cloudAiDocumentOption;
  }
  /**
   * Field mask for creating Document fields. If mask path is empty, it means
   * all fields are masked. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask.
   *
   * @param string $createMask
   */
  public function setCreateMask($createMask)
  {
    $this->createMask = $createMask;
  }
  /**
   * @return string
   */
  public function getCreateMask()
  {
    return $this->createMask;
  }
  /**
   * Required. The document to create.
   *
   * @param GoogleCloudContentwarehouseV1Document $document
   */
  public function setDocument(GoogleCloudContentwarehouseV1Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return GoogleCloudContentwarehouseV1Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Default document policy during creation. This refers to an Identity and
   * Access (IAM) policy, which specifies access controls for the Document.
   * Conditions defined in the policy will be ignored.
   *
   * @param GoogleIamV1Policy $policy
   */
  public function setPolicy(GoogleIamV1Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return GoogleIamV1Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The meta information collected about the end user, used to enforce access
   * control for the service.
   *
   * @param GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1CreateDocumentRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1CreateDocumentRequest');
