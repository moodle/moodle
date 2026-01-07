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

class GoogleCloudContactcenterinsightsV1alpha1SampleConversationsRequest extends \Google\Model
{
  protected $destinationDatasetType = GoogleCloudContactcenterinsightsV1alpha1Dataset::class;
  protected $destinationDatasetDataType = '';
  /**
   * Required. The parent resource of the dataset.
   *
   * @var string
   */
  public $parent;
  protected $sampleRuleType = GoogleCloudContactcenterinsightsV1alpha1SampleRule::class;
  protected $sampleRuleDataType = '';

  /**
   * The dataset resource to copy the sampled conversations to.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1Dataset $destinationDataset
   */
  public function setDestinationDataset(GoogleCloudContactcenterinsightsV1alpha1Dataset $destinationDataset)
  {
    $this->destinationDataset = $destinationDataset;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1Dataset
   */
  public function getDestinationDataset()
  {
    return $this->destinationDataset;
  }
  /**
   * Required. The parent resource of the dataset.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Optional. The sample rule used for sampling conversations.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1SampleRule $sampleRule
   */
  public function setSampleRule(GoogleCloudContactcenterinsightsV1alpha1SampleRule $sampleRule)
  {
    $this->sampleRule = $sampleRule;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1SampleRule
   */
  public function getSampleRule()
  {
    return $this->sampleRule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1SampleConversationsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1SampleConversationsRequest');
