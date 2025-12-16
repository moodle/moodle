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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2Control extends \Google\Collection
{
  protected $collection_key = 'solutionTypes';
  /**
   * Output only. List of serving config ids that are associated with this
   * control in the same Catalog. Note the association is managed via the
   * ServingConfig, this is an output only denormalized view.
   *
   * @var string[]
   */
  public $associatedServingConfigIds;
  /**
   * Required. The human readable control display name. Used in Retail UI. This
   * field must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is thrown.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. Fully qualified name
   * `projects/locations/global/catalogs/controls`
   *
   * @var string
   */
  public $name;
  protected $ruleType = GoogleCloudRetailV2Rule::class;
  protected $ruleDataType = '';
  /**
   * Specifies the use case for the control. Affects what condition fields can
   * be set. Only settable by search controls. Will default to
   * SEARCH_SOLUTION_USE_CASE_SEARCH if not specified. Currently only allow one
   * search_solution_use_case per control.
   *
   * @var string[]
   */
  public $searchSolutionUseCase;
  /**
   * Required. Immutable. The solution types that the control is used for.
   * Currently we support setting only one type of solution at creation time.
   * Only `SOLUTION_TYPE_SEARCH` value is supported at the moment. If no
   * solution type is provided at creation time, will default to
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $solutionTypes;

  /**
   * Output only. List of serving config ids that are associated with this
   * control in the same Catalog. Note the association is managed via the
   * ServingConfig, this is an output only denormalized view.
   *
   * @param string[] $associatedServingConfigIds
   */
  public function setAssociatedServingConfigIds($associatedServingConfigIds)
  {
    $this->associatedServingConfigIds = $associatedServingConfigIds;
  }
  /**
   * @return string[]
   */
  public function getAssociatedServingConfigIds()
  {
    return $this->associatedServingConfigIds;
  }
  /**
   * Required. The human readable control display name. Used in Retail UI. This
   * field must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is thrown.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Immutable. Fully qualified name
   * `projects/locations/global/catalogs/controls`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A rule control - a condition-action pair. Enacts a set action when the
   * condition is triggered. For example: Boost "gShoe" when query full matches
   * "Running Shoes".
   *
   * @param GoogleCloudRetailV2Rule $rule
   */
  public function setRule(GoogleCloudRetailV2Rule $rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return GoogleCloudRetailV2Rule
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * Specifies the use case for the control. Affects what condition fields can
   * be set. Only settable by search controls. Will default to
   * SEARCH_SOLUTION_USE_CASE_SEARCH if not specified. Currently only allow one
   * search_solution_use_case per control.
   *
   * @param string[] $searchSolutionUseCase
   */
  public function setSearchSolutionUseCase($searchSolutionUseCase)
  {
    $this->searchSolutionUseCase = $searchSolutionUseCase;
  }
  /**
   * @return string[]
   */
  public function getSearchSolutionUseCase()
  {
    return $this->searchSolutionUseCase;
  }
  /**
   * Required. Immutable. The solution types that the control is used for.
   * Currently we support setting only one type of solution at creation time.
   * Only `SOLUTION_TYPE_SEARCH` value is supported at the moment. If no
   * solution type is provided at creation time, will default to
   * SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $solutionTypes
   */
  public function setSolutionTypes($solutionTypes)
  {
    $this->solutionTypes = $solutionTypes;
  }
  /**
   * @return string[]
   */
  public function getSolutionTypes()
  {
    return $this->solutionTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Control::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Control');
