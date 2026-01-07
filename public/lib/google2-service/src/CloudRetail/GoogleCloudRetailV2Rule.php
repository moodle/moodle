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

class GoogleCloudRetailV2Rule extends \Google\Model
{
  protected $boostActionType = GoogleCloudRetailV2RuleBoostAction::class;
  protected $boostActionDataType = '';
  protected $conditionType = GoogleCloudRetailV2Condition::class;
  protected $conditionDataType = '';
  protected $doNotAssociateActionType = GoogleCloudRetailV2RuleDoNotAssociateAction::class;
  protected $doNotAssociateActionDataType = '';
  protected $filterActionType = GoogleCloudRetailV2RuleFilterAction::class;
  protected $filterActionDataType = '';
  protected $forceReturnFacetActionType = GoogleCloudRetailV2RuleForceReturnFacetAction::class;
  protected $forceReturnFacetActionDataType = '';
  protected $ignoreActionType = GoogleCloudRetailV2RuleIgnoreAction::class;
  protected $ignoreActionDataType = '';
  protected $onewaySynonymsActionType = GoogleCloudRetailV2RuleOnewaySynonymsAction::class;
  protected $onewaySynonymsActionDataType = '';
  protected $pinActionType = GoogleCloudRetailV2RulePinAction::class;
  protected $pinActionDataType = '';
  protected $redirectActionType = GoogleCloudRetailV2RuleRedirectAction::class;
  protected $redirectActionDataType = '';
  protected $removeFacetActionType = GoogleCloudRetailV2RuleRemoveFacetAction::class;
  protected $removeFacetActionDataType = '';
  protected $replacementActionType = GoogleCloudRetailV2RuleReplacementAction::class;
  protected $replacementActionDataType = '';
  protected $twowaySynonymsActionType = GoogleCloudRetailV2RuleTwowaySynonymsAction::class;
  protected $twowaySynonymsActionDataType = '';

  /**
   * A boost action.
   *
   * @param GoogleCloudRetailV2RuleBoostAction $boostAction
   */
  public function setBoostAction(GoogleCloudRetailV2RuleBoostAction $boostAction)
  {
    $this->boostAction = $boostAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleBoostAction
   */
  public function getBoostAction()
  {
    return $this->boostAction;
  }
  /**
   * Required. The condition that triggers the rule. If the condition is empty,
   * the rule will always apply.
   *
   * @param GoogleCloudRetailV2Condition $condition
   */
  public function setCondition(GoogleCloudRetailV2Condition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return GoogleCloudRetailV2Condition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Prevents term from being associated with other terms.
   *
   * @param GoogleCloudRetailV2RuleDoNotAssociateAction $doNotAssociateAction
   */
  public function setDoNotAssociateAction(GoogleCloudRetailV2RuleDoNotAssociateAction $doNotAssociateAction)
  {
    $this->doNotAssociateAction = $doNotAssociateAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleDoNotAssociateAction
   */
  public function getDoNotAssociateAction()
  {
    return $this->doNotAssociateAction;
  }
  /**
   * Filters results.
   *
   * @param GoogleCloudRetailV2RuleFilterAction $filterAction
   */
  public function setFilterAction(GoogleCloudRetailV2RuleFilterAction $filterAction)
  {
    $this->filterAction = $filterAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleFilterAction
   */
  public function getFilterAction()
  {
    return $this->filterAction;
  }
  /**
   * Force returns an attribute as a facet in the request.
   *
   * @param GoogleCloudRetailV2RuleForceReturnFacetAction $forceReturnFacetAction
   */
  public function setForceReturnFacetAction(GoogleCloudRetailV2RuleForceReturnFacetAction $forceReturnFacetAction)
  {
    $this->forceReturnFacetAction = $forceReturnFacetAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleForceReturnFacetAction
   */
  public function getForceReturnFacetAction()
  {
    return $this->forceReturnFacetAction;
  }
  /**
   * Ignores specific terms from query during search.
   *
   * @param GoogleCloudRetailV2RuleIgnoreAction $ignoreAction
   */
  public function setIgnoreAction(GoogleCloudRetailV2RuleIgnoreAction $ignoreAction)
  {
    $this->ignoreAction = $ignoreAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleIgnoreAction
   */
  public function getIgnoreAction()
  {
    return $this->ignoreAction;
  }
  /**
   * Treats specific term as a synonym with a group of terms. Group of terms
   * will not be treated as synonyms with the specific term.
   *
   * @param GoogleCloudRetailV2RuleOnewaySynonymsAction $onewaySynonymsAction
   */
  public function setOnewaySynonymsAction(GoogleCloudRetailV2RuleOnewaySynonymsAction $onewaySynonymsAction)
  {
    $this->onewaySynonymsAction = $onewaySynonymsAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleOnewaySynonymsAction
   */
  public function getOnewaySynonymsAction()
  {
    return $this->onewaySynonymsAction;
  }
  /**
   * Pins one or more specified products to a specific position in the results.
   *
   * @param GoogleCloudRetailV2RulePinAction $pinAction
   */
  public function setPinAction(GoogleCloudRetailV2RulePinAction $pinAction)
  {
    $this->pinAction = $pinAction;
  }
  /**
   * @return GoogleCloudRetailV2RulePinAction
   */
  public function getPinAction()
  {
    return $this->pinAction;
  }
  /**
   * Redirects a shopper to a specific page.
   *
   * @param GoogleCloudRetailV2RuleRedirectAction $redirectAction
   */
  public function setRedirectAction(GoogleCloudRetailV2RuleRedirectAction $redirectAction)
  {
    $this->redirectAction = $redirectAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleRedirectAction
   */
  public function getRedirectAction()
  {
    return $this->redirectAction;
  }
  /**
   * Remove an attribute as a facet in the request (if present).
   *
   * @param GoogleCloudRetailV2RuleRemoveFacetAction $removeFacetAction
   */
  public function setRemoveFacetAction(GoogleCloudRetailV2RuleRemoveFacetAction $removeFacetAction)
  {
    $this->removeFacetAction = $removeFacetAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleRemoveFacetAction
   */
  public function getRemoveFacetAction()
  {
    return $this->removeFacetAction;
  }
  /**
   * Replaces specific terms in the query.
   *
   * @param GoogleCloudRetailV2RuleReplacementAction $replacementAction
   */
  public function setReplacementAction(GoogleCloudRetailV2RuleReplacementAction $replacementAction)
  {
    $this->replacementAction = $replacementAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleReplacementAction
   */
  public function getReplacementAction()
  {
    return $this->replacementAction;
  }
  /**
   * Treats a set of terms as synonyms of one another.
   *
   * @param GoogleCloudRetailV2RuleTwowaySynonymsAction $twowaySynonymsAction
   */
  public function setTwowaySynonymsAction(GoogleCloudRetailV2RuleTwowaySynonymsAction $twowaySynonymsAction)
  {
    $this->twowaySynonymsAction = $twowaySynonymsAction;
  }
  /**
   * @return GoogleCloudRetailV2RuleTwowaySynonymsAction
   */
  public function getTwowaySynonymsAction()
  {
    return $this->twowaySynonymsAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Rule::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Rule');
