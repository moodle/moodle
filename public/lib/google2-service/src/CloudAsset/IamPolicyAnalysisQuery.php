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

namespace Google\Service\CloudAsset;

class IamPolicyAnalysisQuery extends \Google\Model
{
  protected $accessSelectorType = AccessSelector::class;
  protected $accessSelectorDataType = '';
  protected $conditionContextType = ConditionContext::class;
  protected $conditionContextDataType = '';
  protected $identitySelectorType = IdentitySelector::class;
  protected $identitySelectorDataType = '';
  protected $optionsType = Options::class;
  protected $optionsDataType = '';
  protected $resourceSelectorType = ResourceSelector::class;
  protected $resourceSelectorDataType = '';
  /**
   * Required. The relative name of the root asset. Only resources and IAM
   * policies within the scope will be analyzed. This can only be an
   * organization number (such as "organizations/123"), a folder number (such as
   * "folders/123"), a project ID (such as "projects/my-project-id"), or a
   * project number (such as "projects/12345"). To know how to get organization
   * ID, visit [here ](https://cloud.google.com/resource-manager/docs/creating-
   * managing-organization#retrieving_your_organization_id). To know how to get
   * folder or project ID, visit [here ](https://cloud.google.com/resource-
   * manager/docs/creating-managing-
   * folders#viewing_or_listing_folders_and_projects).
   *
   * @var string
   */
  public $scope;

  /**
   * Optional. Specifies roles or permissions for analysis. This is optional.
   *
   * @param AccessSelector $accessSelector
   */
  public function setAccessSelector(AccessSelector $accessSelector)
  {
    $this->accessSelector = $accessSelector;
  }
  /**
   * @return AccessSelector
   */
  public function getAccessSelector()
  {
    return $this->accessSelector;
  }
  /**
   * Optional. The hypothetical context for IAM conditions evaluation.
   *
   * @param ConditionContext $conditionContext
   */
  public function setConditionContext(ConditionContext $conditionContext)
  {
    $this->conditionContext = $conditionContext;
  }
  /**
   * @return ConditionContext
   */
  public function getConditionContext()
  {
    return $this->conditionContext;
  }
  /**
   * Optional. Specifies an identity for analysis.
   *
   * @param IdentitySelector $identitySelector
   */
  public function setIdentitySelector(IdentitySelector $identitySelector)
  {
    $this->identitySelector = $identitySelector;
  }
  /**
   * @return IdentitySelector
   */
  public function getIdentitySelector()
  {
    return $this->identitySelector;
  }
  /**
   * Optional. The query options.
   *
   * @param Options $options
   */
  public function setOptions(Options $options)
  {
    $this->options = $options;
  }
  /**
   * @return Options
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Optional. Specifies a resource for analysis.
   *
   * @param ResourceSelector $resourceSelector
   */
  public function setResourceSelector(ResourceSelector $resourceSelector)
  {
    $this->resourceSelector = $resourceSelector;
  }
  /**
   * @return ResourceSelector
   */
  public function getResourceSelector()
  {
    return $this->resourceSelector;
  }
  /**
   * Required. The relative name of the root asset. Only resources and IAM
   * policies within the scope will be analyzed. This can only be an
   * organization number (such as "organizations/123"), a folder number (such as
   * "folders/123"), a project ID (such as "projects/my-project-id"), or a
   * project number (such as "projects/12345"). To know how to get organization
   * ID, visit [here ](https://cloud.google.com/resource-manager/docs/creating-
   * managing-organization#retrieving_your_organization_id). To know how to get
   * folder or project ID, visit [here ](https://cloud.google.com/resource-
   * manager/docs/creating-managing-
   * folders#viewing_or_listing_folders_and_projects).
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicyAnalysisQuery::class, 'Google_Service_CloudAsset_IamPolicyAnalysisQuery');
