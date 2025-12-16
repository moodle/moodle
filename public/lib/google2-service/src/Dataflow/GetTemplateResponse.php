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

namespace Google\Service\Dataflow;

class GetTemplateResponse extends \Google\Model
{
  /**
   * Unknown Template Type.
   */
  public const TEMPLATE_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Legacy Template.
   */
  public const TEMPLATE_TYPE_LEGACY = 'LEGACY';
  /**
   * Flex Template.
   */
  public const TEMPLATE_TYPE_FLEX = 'FLEX';
  protected $metadataType = TemplateMetadata::class;
  protected $metadataDataType = '';
  protected $runtimeMetadataType = RuntimeMetadata::class;
  protected $runtimeMetadataDataType = '';
  protected $statusType = Status::class;
  protected $statusDataType = '';
  /**
   * Template Type.
   *
   * @var string
   */
  public $templateType;

  /**
   * The template metadata describing the template name, available parameters,
   * etc.
   *
   * @param TemplateMetadata $metadata
   */
  public function setMetadata(TemplateMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return TemplateMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Describes the runtime metadata with SDKInfo and available parameters.
   *
   * @param RuntimeMetadata $runtimeMetadata
   */
  public function setRuntimeMetadata(RuntimeMetadata $runtimeMetadata)
  {
    $this->runtimeMetadata = $runtimeMetadata;
  }
  /**
   * @return RuntimeMetadata
   */
  public function getRuntimeMetadata()
  {
    return $this->runtimeMetadata;
  }
  /**
   * The status of the get template request. Any problems with the request will
   * be indicated in the error_details.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Template Type.
   *
   * Accepted values: UNKNOWN, LEGACY, FLEX
   *
   * @param self::TEMPLATE_TYPE_* $templateType
   */
  public function setTemplateType($templateType)
  {
    $this->templateType = $templateType;
  }
  /**
   * @return self::TEMPLATE_TYPE_*
   */
  public function getTemplateType()
  {
    return $this->templateType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetTemplateResponse::class, 'Google_Service_Dataflow_GetTemplateResponse');
