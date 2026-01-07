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

class IamPolicyAnalysisResult extends \Google\Collection
{
  protected $collection_key = 'accessControlLists';
  protected $accessControlListsType = GoogleCloudAssetV1AccessControlList::class;
  protected $accessControlListsDataType = 'array';
  /**
   * The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the resource to which the
   * iam_binding policy attaches.
   *
   * @var string
   */
  public $attachedResourceFullName;
  /**
   * Represents whether all analyses on the iam_binding have successfully
   * finished.
   *
   * @var bool
   */
  public $fullyExplored;
  protected $iamBindingType = Binding::class;
  protected $iamBindingDataType = '';
  protected $identityListType = GoogleCloudAssetV1IdentityList::class;
  protected $identityListDataType = '';

  /**
   * The access control lists derived from the iam_binding that match or
   * potentially match resource and access selectors specified in the request.
   *
   * @param GoogleCloudAssetV1AccessControlList[] $accessControlLists
   */
  public function setAccessControlLists($accessControlLists)
  {
    $this->accessControlLists = $accessControlLists;
  }
  /**
   * @return GoogleCloudAssetV1AccessControlList[]
   */
  public function getAccessControlLists()
  {
    return $this->accessControlLists;
  }
  /**
   * The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the resource to which the
   * iam_binding policy attaches.
   *
   * @param string $attachedResourceFullName
   */
  public function setAttachedResourceFullName($attachedResourceFullName)
  {
    $this->attachedResourceFullName = $attachedResourceFullName;
  }
  /**
   * @return string
   */
  public function getAttachedResourceFullName()
  {
    return $this->attachedResourceFullName;
  }
  /**
   * Represents whether all analyses on the iam_binding have successfully
   * finished.
   *
   * @param bool $fullyExplored
   */
  public function setFullyExplored($fullyExplored)
  {
    $this->fullyExplored = $fullyExplored;
  }
  /**
   * @return bool
   */
  public function getFullyExplored()
  {
    return $this->fullyExplored;
  }
  /**
   * The IAM policy binding under analysis.
   *
   * @param Binding $iamBinding
   */
  public function setIamBinding(Binding $iamBinding)
  {
    $this->iamBinding = $iamBinding;
  }
  /**
   * @return Binding
   */
  public function getIamBinding()
  {
    return $this->iamBinding;
  }
  /**
   * The identity list derived from members of the iam_binding that match or
   * potentially match identity selector specified in the request.
   *
   * @param GoogleCloudAssetV1IdentityList $identityList
   */
  public function setIdentityList(GoogleCloudAssetV1IdentityList $identityList)
  {
    $this->identityList = $identityList;
  }
  /**
   * @return GoogleCloudAssetV1IdentityList
   */
  public function getIdentityList()
  {
    return $this->identityList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicyAnalysisResult::class, 'Google_Service_CloudAsset_IamPolicyAnalysisResult');
