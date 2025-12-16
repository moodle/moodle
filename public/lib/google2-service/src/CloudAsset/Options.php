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

class Options extends \Google\Model
{
  /**
   * Optional. If true, the response will include access analysis from
   * identities to resources via service account impersonation. This is a very
   * expensive operation, because many derived queries will be executed. We
   * highly recommend you use AssetService.AnalyzeIamPolicyLongrunning RPC
   * instead. For example, if the request analyzes for which resources user A
   * has permission P, and there's an IAM policy states user A has
   * iam.serviceAccounts.getAccessToken permission to a service account SA, and
   * there's another IAM policy states service account SA has permission P to a
   * Google Cloud folder F, then user A potentially has access to the Google
   * Cloud folder F. And those advanced analysis results will be included in
   * AnalyzeIamPolicyResponse.service_account_impersonation_analysis. Another
   * example, if the request analyzes for who has permission P to a Google Cloud
   * folder F, and there's an IAM policy states user A has
   * iam.serviceAccounts.actAs permission to a service account SA, and there's
   * another IAM policy states service account SA has permission P to the Google
   * Cloud folder F, then user A potentially has access to the Google Cloud
   * folder F. And those advanced analysis results will be included in
   * AnalyzeIamPolicyResponse.service_account_impersonation_analysis. Only the
   * following permissions are considered in this analysis: *
   * `iam.serviceAccounts.actAs` * `iam.serviceAccounts.signBlob` *
   * `iam.serviceAccounts.signJwt` * `iam.serviceAccounts.getAccessToken` *
   * `iam.serviceAccounts.getOpenIdToken` *
   * `iam.serviceAccounts.implicitDelegation` Default is false.
   *
   * @var bool
   */
  public $analyzeServiceAccountImpersonation;
  /**
   * Optional. If true, the identities section of the result will expand any
   * Google groups appearing in an IAM policy binding. If
   * IamPolicyAnalysisQuery.identity_selector is specified, the identity in the
   * result will be determined by the selector, and this flag is not allowed to
   * set. If true, the default max expansion per group is 1000 for
   * AssetService.AnalyzeIamPolicy][]. Default is false.
   *
   * @var bool
   */
  public $expandGroups;
  /**
   * Optional. If true and IamPolicyAnalysisQuery.resource_selector is not
   * specified, the resource section of the result will expand any resource
   * attached to an IAM policy to include resources lower in the resource
   * hierarchy. For example, if the request analyzes for which resources user A
   * has permission P, and the results include an IAM policy with P on a Google
   * Cloud folder, the results will also include resources in that folder with
   * permission P. If true and IamPolicyAnalysisQuery.resource_selector is
   * specified, the resource section of the result will expand the specified
   * resource to include resources lower in the resource hierarchy. Only project
   * or lower resources are supported. Folder and organization resources cannot
   * be used together with this option. For example, if the request analyzes for
   * which users have permission P on a Google Cloud project with this option
   * enabled, the results will include all users who have permission P on that
   * project or any lower resource. If true, the default max expansion per
   * resource is 1000 for AssetService.AnalyzeIamPolicy][] and 100000 for
   * AssetService.AnalyzeIamPolicyLongrunning][]. Default is false.
   *
   * @var bool
   */
  public $expandResources;
  /**
   * Optional. If true, the access section of result will expand any roles
   * appearing in IAM policy bindings to include their permissions. If
   * IamPolicyAnalysisQuery.access_selector is specified, the access section of
   * the result will be determined by the selector, and this flag is not allowed
   * to set. Default is false.
   *
   * @var bool
   */
  public $expandRoles;
  /**
   * Optional. If true, the result will output the relevant membership
   * relationships between groups and other groups, and between groups and
   * principals. Default is false.
   *
   * @var bool
   */
  public $outputGroupEdges;
  /**
   * Optional. If true, the result will output the relevant parent/child
   * relationships between resources. Default is false.
   *
   * @var bool
   */
  public $outputResourceEdges;

  /**
   * Optional. If true, the response will include access analysis from
   * identities to resources via service account impersonation. This is a very
   * expensive operation, because many derived queries will be executed. We
   * highly recommend you use AssetService.AnalyzeIamPolicyLongrunning RPC
   * instead. For example, if the request analyzes for which resources user A
   * has permission P, and there's an IAM policy states user A has
   * iam.serviceAccounts.getAccessToken permission to a service account SA, and
   * there's another IAM policy states service account SA has permission P to a
   * Google Cloud folder F, then user A potentially has access to the Google
   * Cloud folder F. And those advanced analysis results will be included in
   * AnalyzeIamPolicyResponse.service_account_impersonation_analysis. Another
   * example, if the request analyzes for who has permission P to a Google Cloud
   * folder F, and there's an IAM policy states user A has
   * iam.serviceAccounts.actAs permission to a service account SA, and there's
   * another IAM policy states service account SA has permission P to the Google
   * Cloud folder F, then user A potentially has access to the Google Cloud
   * folder F. And those advanced analysis results will be included in
   * AnalyzeIamPolicyResponse.service_account_impersonation_analysis. Only the
   * following permissions are considered in this analysis: *
   * `iam.serviceAccounts.actAs` * `iam.serviceAccounts.signBlob` *
   * `iam.serviceAccounts.signJwt` * `iam.serviceAccounts.getAccessToken` *
   * `iam.serviceAccounts.getOpenIdToken` *
   * `iam.serviceAccounts.implicitDelegation` Default is false.
   *
   * @param bool $analyzeServiceAccountImpersonation
   */
  public function setAnalyzeServiceAccountImpersonation($analyzeServiceAccountImpersonation)
  {
    $this->analyzeServiceAccountImpersonation = $analyzeServiceAccountImpersonation;
  }
  /**
   * @return bool
   */
  public function getAnalyzeServiceAccountImpersonation()
  {
    return $this->analyzeServiceAccountImpersonation;
  }
  /**
   * Optional. If true, the identities section of the result will expand any
   * Google groups appearing in an IAM policy binding. If
   * IamPolicyAnalysisQuery.identity_selector is specified, the identity in the
   * result will be determined by the selector, and this flag is not allowed to
   * set. If true, the default max expansion per group is 1000 for
   * AssetService.AnalyzeIamPolicy][]. Default is false.
   *
   * @param bool $expandGroups
   */
  public function setExpandGroups($expandGroups)
  {
    $this->expandGroups = $expandGroups;
  }
  /**
   * @return bool
   */
  public function getExpandGroups()
  {
    return $this->expandGroups;
  }
  /**
   * Optional. If true and IamPolicyAnalysisQuery.resource_selector is not
   * specified, the resource section of the result will expand any resource
   * attached to an IAM policy to include resources lower in the resource
   * hierarchy. For example, if the request analyzes for which resources user A
   * has permission P, and the results include an IAM policy with P on a Google
   * Cloud folder, the results will also include resources in that folder with
   * permission P. If true and IamPolicyAnalysisQuery.resource_selector is
   * specified, the resource section of the result will expand the specified
   * resource to include resources lower in the resource hierarchy. Only project
   * or lower resources are supported. Folder and organization resources cannot
   * be used together with this option. For example, if the request analyzes for
   * which users have permission P on a Google Cloud project with this option
   * enabled, the results will include all users who have permission P on that
   * project or any lower resource. If true, the default max expansion per
   * resource is 1000 for AssetService.AnalyzeIamPolicy][] and 100000 for
   * AssetService.AnalyzeIamPolicyLongrunning][]. Default is false.
   *
   * @param bool $expandResources
   */
  public function setExpandResources($expandResources)
  {
    $this->expandResources = $expandResources;
  }
  /**
   * @return bool
   */
  public function getExpandResources()
  {
    return $this->expandResources;
  }
  /**
   * Optional. If true, the access section of result will expand any roles
   * appearing in IAM policy bindings to include their permissions. If
   * IamPolicyAnalysisQuery.access_selector is specified, the access section of
   * the result will be determined by the selector, and this flag is not allowed
   * to set. Default is false.
   *
   * @param bool $expandRoles
   */
  public function setExpandRoles($expandRoles)
  {
    $this->expandRoles = $expandRoles;
  }
  /**
   * @return bool
   */
  public function getExpandRoles()
  {
    return $this->expandRoles;
  }
  /**
   * Optional. If true, the result will output the relevant membership
   * relationships between groups and other groups, and between groups and
   * principals. Default is false.
   *
   * @param bool $outputGroupEdges
   */
  public function setOutputGroupEdges($outputGroupEdges)
  {
    $this->outputGroupEdges = $outputGroupEdges;
  }
  /**
   * @return bool
   */
  public function getOutputGroupEdges()
  {
    return $this->outputGroupEdges;
  }
  /**
   * Optional. If true, the result will output the relevant parent/child
   * relationships between resources. Default is false.
   *
   * @param bool $outputResourceEdges
   */
  public function setOutputResourceEdges($outputResourceEdges)
  {
    $this->outputResourceEdges = $outputResourceEdges;
  }
  /**
   * @return bool
   */
  public function getOutputResourceEdges()
  {
    return $this->outputResourceEdges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Options::class, 'Google_Service_CloudAsset_Options');
