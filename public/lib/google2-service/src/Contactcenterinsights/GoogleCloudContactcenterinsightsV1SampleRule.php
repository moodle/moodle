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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1SampleRule extends \Google\Model
{
  /**
   * To specify the filter for the conversions that should apply this sample
   * rule. An empty filter means this sample rule applies to all conversations.
   *
   * @var string
   */
  public $conversationFilter;
  /**
   * Optional. Group by dimension to sample the conversation. If no dimension is
   * provided, the sampling will be applied to the project level. Current
   * supported dimensions is 'quality_metadata.agent_info.agent_id'.
   *
   * @var string
   */
  public $dimension;
  /**
   * Percentage of conversations that we should sample based on the dimension
   * between [0, 100].
   *
   * @var 
   */
  public $samplePercentage;
  /**
   * Number of the conversations that we should sample based on the dimension.
   *
   * @var string
   */
  public $sampleRow;

  /**
   * To specify the filter for the conversions that should apply this sample
   * rule. An empty filter means this sample rule applies to all conversations.
   *
   * @param string $conversationFilter
   */
  public function setConversationFilter($conversationFilter)
  {
    $this->conversationFilter = $conversationFilter;
  }
  /**
   * @return string
   */
  public function getConversationFilter()
  {
    return $this->conversationFilter;
  }
  /**
   * Optional. Group by dimension to sample the conversation. If no dimension is
   * provided, the sampling will be applied to the project level. Current
   * supported dimensions is 'quality_metadata.agent_info.agent_id'.
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return string
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  public function setSamplePercentage($samplePercentage)
  {
    $this->samplePercentage = $samplePercentage;
  }
  public function getSamplePercentage()
  {
    return $this->samplePercentage;
  }
  /**
   * Number of the conversations that we should sample based on the dimension.
   *
   * @param string $sampleRow
   */
  public function setSampleRow($sampleRow)
  {
    $this->sampleRow = $sampleRow;
  }
  /**
   * @return string
   */
  public function getSampleRow()
  {
    return $this->sampleRow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1SampleRule::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1SampleRule');
