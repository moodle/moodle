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

class AnalyzeOrgPolicyGovernedAssetsResponse extends \Google\Collection
{
  protected $collection_key = 'governedAssets';
  protected $constraintType = AnalyzerOrgPolicyConstraint::class;
  protected $constraintDataType = '';
  protected $governedAssetsType = GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedAsset::class;
  protected $governedAssetsDataType = 'array';
  /**
   * The page token to fetch the next page for
   * AnalyzeOrgPolicyGovernedAssetsResponse.governed_assets.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The definition of the constraint in the request.
   *
   * @param AnalyzerOrgPolicyConstraint $constraint
   */
  public function setConstraint(AnalyzerOrgPolicyConstraint $constraint)
  {
    $this->constraint = $constraint;
  }
  /**
   * @return AnalyzerOrgPolicyConstraint
   */
  public function getConstraint()
  {
    return $this->constraint;
  }
  /**
   * The list of the analyzed governed assets.
   *
   * @param GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedAsset[] $governedAssets
   */
  public function setGovernedAssets($governedAssets)
  {
    $this->governedAssets = $governedAssets;
  }
  /**
   * @return GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedAsset[]
   */
  public function getGovernedAssets()
  {
    return $this->governedAssets;
  }
  /**
   * The page token to fetch the next page for
   * AnalyzeOrgPolicyGovernedAssetsResponse.governed_assets.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeOrgPolicyGovernedAssetsResponse::class, 'Google_Service_CloudAsset_AnalyzeOrgPolicyGovernedAssetsResponse');
