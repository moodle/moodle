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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SerializedPolicyTag extends \Google\Collection
{
  protected $collection_key = 'childPolicyTags';
  protected $childPolicyTagsType = GoogleCloudDatacatalogV1SerializedPolicyTag::class;
  protected $childPolicyTagsDataType = 'array';
  /**
   * Description of the serialized policy tag. At most 2000 bytes when encoded
   * in UTF-8. If not set, defaults to an empty description.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name of the policy tag. At most 200 bytes when encoded in
   * UTF-8.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of the policy tag. This field is ignored when calling
   * `ImportTaxonomies`.
   *
   * @var string
   */
  public $policyTag;

  /**
   * Children of the policy tag, if any.
   *
   * @param GoogleCloudDatacatalogV1SerializedPolicyTag[] $childPolicyTags
   */
  public function setChildPolicyTags($childPolicyTags)
  {
    $this->childPolicyTags = $childPolicyTags;
  }
  /**
   * @return GoogleCloudDatacatalogV1SerializedPolicyTag[]
   */
  public function getChildPolicyTags()
  {
    return $this->childPolicyTags;
  }
  /**
   * Description of the serialized policy tag. At most 2000 bytes when encoded
   * in UTF-8. If not set, defaults to an empty description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Display name of the policy tag. At most 200 bytes when encoded in
   * UTF-8.
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
   * Resource name of the policy tag. This field is ignored when calling
   * `ImportTaxonomies`.
   *
   * @param string $policyTag
   */
  public function setPolicyTag($policyTag)
  {
    $this->policyTag = $policyTag;
  }
  /**
   * @return string
   */
  public function getPolicyTag()
  {
    return $this->policyTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SerializedPolicyTag::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SerializedPolicyTag');
