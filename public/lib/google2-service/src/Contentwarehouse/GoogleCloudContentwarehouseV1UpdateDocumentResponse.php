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

class GoogleCloudContentwarehouseV1UpdateDocumentResponse extends \Google\Model
{
  protected $documentType = GoogleCloudContentwarehouseV1Document::class;
  protected $documentDataType = '';
  protected $metadataType = GoogleCloudContentwarehouseV1ResponseMetadata::class;
  protected $metadataDataType = '';
  protected $ruleEngineOutputType = GoogleCloudContentwarehouseV1RuleEngineOutput::class;
  protected $ruleEngineOutputDataType = '';

  /**
   * Updated document after executing update request.
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
   * Additional information for the API invocation, such as the request tracking
   * id.
   *
   * @param GoogleCloudContentwarehouseV1ResponseMetadata $metadata
   */
  public function setMetadata(GoogleCloudContentwarehouseV1ResponseMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1ResponseMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output from Rule Engine recording the rule evaluator and action executor's
   * output. Refer format in: google/cloud/contentwarehouse/v1/rule_engine.proto
   *
   * @param GoogleCloudContentwarehouseV1RuleEngineOutput $ruleEngineOutput
   */
  public function setRuleEngineOutput(GoogleCloudContentwarehouseV1RuleEngineOutput $ruleEngineOutput)
  {
    $this->ruleEngineOutput = $ruleEngineOutput;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RuleEngineOutput
   */
  public function getRuleEngineOutput()
  {
    return $this->ruleEngineOutput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1UpdateDocumentResponse::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1UpdateDocumentResponse');
