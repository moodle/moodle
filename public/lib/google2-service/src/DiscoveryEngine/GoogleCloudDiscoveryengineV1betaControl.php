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

class GoogleCloudDiscoveryengineV1betaControl extends \Google\Collection
{
  /**
   * Default value.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_UNSPECIFIED = 'SOLUTION_TYPE_UNSPECIFIED';
  /**
   * Used for Recommendations AI.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_RECOMMENDATION = 'SOLUTION_TYPE_RECOMMENDATION';
  /**
   * Used for Discovery Search.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_SEARCH = 'SOLUTION_TYPE_SEARCH';
  /**
   * Used for use cases related to the Generative AI agent.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_CHAT = 'SOLUTION_TYPE_CHAT';
  /**
   * Used for use cases related to the Generative Chat agent. It's used for
   * Generative chat engine only, the associated data stores must enrolled with
   * `SOLUTION_TYPE_CHAT` solution.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_GENERATIVE_CHAT = 'SOLUTION_TYPE_GENERATIVE_CHAT';
  protected $collection_key = 'useCases';
  /**
   * Output only. List of all ServingConfig IDs this control is attached to. May
   * take up to 10 minutes to update after changes.
   *
   * @var string[]
   */
  public $associatedServingConfigIds;
  protected $boostActionType = GoogleCloudDiscoveryengineV1betaControlBoostAction::class;
  protected $boostActionDataType = '';
  protected $conditionsType = GoogleCloudDiscoveryengineV1betaCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * Required. Human readable name. The identifier used in UI views. Must be
   * UTF-8 encoded string. Length limit is 128 characters. Otherwise an INVALID
   * ARGUMENT error is thrown.
   *
   * @var string
   */
  public $displayName;
  protected $filterActionType = GoogleCloudDiscoveryengineV1betaControlFilterAction::class;
  protected $filterActionDataType = '';
  /**
   * Immutable. Fully qualified name
   * `projects/locations/global/dataStore/controls`
   *
   * @var string
   */
  public $name;
  protected $promoteActionType = GoogleCloudDiscoveryengineV1betaControlPromoteAction::class;
  protected $promoteActionDataType = '';
  protected $redirectActionType = GoogleCloudDiscoveryengineV1betaControlRedirectAction::class;
  protected $redirectActionDataType = '';
  /**
   * Required. Immutable. What solution the control belongs to. Must be
   * compatible with vertical of resource. Otherwise an INVALID ARGUMENT error
   * is thrown.
   *
   * @var string
   */
  public $solutionType;
  protected $synonymsActionType = GoogleCloudDiscoveryengineV1betaControlSynonymsAction::class;
  protected $synonymsActionDataType = '';
  /**
   * Specifies the use case for the control. Affects what condition fields can
   * be set. Only applies to SOLUTION_TYPE_SEARCH. Currently only allow one use
   * case per control. Must be set when solution_type is
   * SolutionType.SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $useCases;

  /**
   * Output only. List of all ServingConfig IDs this control is attached to. May
   * take up to 10 minutes to update after changes.
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
   * Defines a boost-type control
   *
   * @param GoogleCloudDiscoveryengineV1betaControlBoostAction $boostAction
   */
  public function setBoostAction(GoogleCloudDiscoveryengineV1betaControlBoostAction $boostAction)
  {
    $this->boostAction = $boostAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlBoostAction
   */
  public function getBoostAction()
  {
    return $this->boostAction;
  }
  /**
   * Determines when the associated action will trigger. Omit to always apply
   * the action. Currently only a single condition may be specified. Otherwise
   * an INVALID ARGUMENT error is thrown.
   *
   * @param GoogleCloudDiscoveryengineV1betaCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Required. Human readable name. The identifier used in UI views. Must be
   * UTF-8 encoded string. Length limit is 128 characters. Otherwise an INVALID
   * ARGUMENT error is thrown.
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
   * Defines a filter-type control Currently not supported by Recommendation
   *
   * @param GoogleCloudDiscoveryengineV1betaControlFilterAction $filterAction
   */
  public function setFilterAction(GoogleCloudDiscoveryengineV1betaControlFilterAction $filterAction)
  {
    $this->filterAction = $filterAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlFilterAction
   */
  public function getFilterAction()
  {
    return $this->filterAction;
  }
  /**
   * Immutable. Fully qualified name
   * `projects/locations/global/dataStore/controls`
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
   * Promote certain links based on predefined trigger queries.
   *
   * @param GoogleCloudDiscoveryengineV1betaControlPromoteAction $promoteAction
   */
  public function setPromoteAction(GoogleCloudDiscoveryengineV1betaControlPromoteAction $promoteAction)
  {
    $this->promoteAction = $promoteAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlPromoteAction
   */
  public function getPromoteAction()
  {
    return $this->promoteAction;
  }
  /**
   * Defines a redirect-type control.
   *
   * @param GoogleCloudDiscoveryengineV1betaControlRedirectAction $redirectAction
   */
  public function setRedirectAction(GoogleCloudDiscoveryengineV1betaControlRedirectAction $redirectAction)
  {
    $this->redirectAction = $redirectAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlRedirectAction
   */
  public function getRedirectAction()
  {
    return $this->redirectAction;
  }
  /**
   * Required. Immutable. What solution the control belongs to. Must be
   * compatible with vertical of resource. Otherwise an INVALID ARGUMENT error
   * is thrown.
   *
   * Accepted values: SOLUTION_TYPE_UNSPECIFIED, SOLUTION_TYPE_RECOMMENDATION,
   * SOLUTION_TYPE_SEARCH, SOLUTION_TYPE_CHAT, SOLUTION_TYPE_GENERATIVE_CHAT
   *
   * @param self::SOLUTION_TYPE_* $solutionType
   */
  public function setSolutionType($solutionType)
  {
    $this->solutionType = $solutionType;
  }
  /**
   * @return self::SOLUTION_TYPE_*
   */
  public function getSolutionType()
  {
    return $this->solutionType;
  }
  /**
   * Treats a group of terms as synonyms of one another.
   *
   * @param GoogleCloudDiscoveryengineV1betaControlSynonymsAction $synonymsAction
   */
  public function setSynonymsAction(GoogleCloudDiscoveryengineV1betaControlSynonymsAction $synonymsAction)
  {
    $this->synonymsAction = $synonymsAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlSynonymsAction
   */
  public function getSynonymsAction()
  {
    return $this->synonymsAction;
  }
  /**
   * Specifies the use case for the control. Affects what condition fields can
   * be set. Only applies to SOLUTION_TYPE_SEARCH. Currently only allow one use
   * case per control. Must be set when solution_type is
   * SolutionType.SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $useCases
   */
  public function setUseCases($useCases)
  {
    $this->useCases = $useCases;
  }
  /**
   * @return string[]
   */
  public function getUseCases()
  {
    return $this->useCases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaControl::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaControl');
