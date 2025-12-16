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

class GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest extends \Google\Model
{
  /**
   * Required. Percentage of selected conversation to analyze, between [0, 100].
   *
   * @var float
   */
  public $analysisPercentage;
  protected $annotatorSelectorType = GoogleCloudContactcenterinsightsV1alpha1AnnotatorSelector::class;
  protected $annotatorSelectorDataType = '';
  /**
   * Required. Filter used to select the subset of conversations to analyze.
   *
   * @var string
   */
  public $filter;
  /**
   * Required. The parent resource to create analyses in.
   *
   * @var string
   */
  public $parent;

  /**
   * Required. Percentage of selected conversation to analyze, between [0, 100].
   *
   * @param float $analysisPercentage
   */
  public function setAnalysisPercentage($analysisPercentage)
  {
    $this->analysisPercentage = $analysisPercentage;
  }
  /**
   * @return float
   */
  public function getAnalysisPercentage()
  {
    return $this->analysisPercentage;
  }
  /**
   * To select the annotators to run and the phrase matchers to use (if any). If
   * not specified, all annotators will be run.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1AnnotatorSelector $annotatorSelector
   */
  public function setAnnotatorSelector(GoogleCloudContactcenterinsightsV1alpha1AnnotatorSelector $annotatorSelector)
  {
    $this->annotatorSelector = $annotatorSelector;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1AnnotatorSelector
   */
  public function getAnnotatorSelector()
  {
    return $this->annotatorSelector;
  }
  /**
   * Required. Filter used to select the subset of conversations to analyze.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. The parent resource to create analyses in.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest');
