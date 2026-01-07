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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1GenerateGroundedContentRequest extends \Google\Collection
{
  protected $collection_key = 'contents';
  protected $contentsType = GoogleCloudDiscoveryengineV1GroundedGenerationContent::class;
  protected $contentsDataType = 'array';
  protected $generationSpecType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec::class;
  protected $generationSpecDataType = '';
  protected $groundingSpecType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec::class;
  protected $groundingSpecDataType = '';
  protected $systemInstructionType = GoogleCloudDiscoveryengineV1GroundedGenerationContent::class;
  protected $systemInstructionDataType = '';
  /**
   * @var string[]
   */
  public $userLabels;

  /**
   * @param GoogleCloudDiscoveryengineV1GroundedGenerationContent[]
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GroundedGenerationContent[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec
   */
  public function setGenerationSpec(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec $generationSpec)
  {
    $this->generationSpec = $generationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGenerationSpec
   */
  public function getGenerationSpec()
  {
    return $this->generationSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec
   */
  public function setGroundingSpec(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec $groundingSpec)
  {
    $this->groundingSpec = $groundingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec
   */
  public function getGroundingSpec()
  {
    return $this->groundingSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GroundedGenerationContent
   */
  public function setSystemInstruction(GoogleCloudDiscoveryengineV1GroundedGenerationContent $systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GroundedGenerationContent
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
  /**
   * @param string[]
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentRequest');
