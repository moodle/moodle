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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TagResources extends \Google\Collection
{
  protected $collection_key = 'tagConditions';
  /**
   * Whether applying a tag to a resource should lower the risk of the profile
   * for that resource. For example, in conjunction with an [IAM deny
   * policy](https://cloud.google.com/iam/docs/deny-overview), you can deny all
   * principals a permission if a tag value is present, mitigating the risk of
   * the resource. This also lowers the data risk of resources at the lower
   * levels of the resource hierarchy. For example, reducing the data risk of a
   * table data profile also reduces the data risk of the constituent column
   * data profiles.
   *
   * @var bool
   */
  public $lowerDataRiskToLow;
  /**
   * The profile generations for which the tag should be attached to resources.
   * If you attach a tag to only new profiles, then if the sensitivity score of
   * a profile subsequently changes, its tag doesn't change. By default, this
   * field includes only new profiles. To include both new and updated profiles
   * for tagging, this field should explicitly include both
   * `PROFILE_GENERATION_NEW` and `PROFILE_GENERATION_UPDATE`.
   *
   * @var string[]
   */
  public $profileGenerationsToTag;
  protected $tagConditionsType = GooglePrivacyDlpV2TagCondition::class;
  protected $tagConditionsDataType = 'array';

  /**
   * Whether applying a tag to a resource should lower the risk of the profile
   * for that resource. For example, in conjunction with an [IAM deny
   * policy](https://cloud.google.com/iam/docs/deny-overview), you can deny all
   * principals a permission if a tag value is present, mitigating the risk of
   * the resource. This also lowers the data risk of resources at the lower
   * levels of the resource hierarchy. For example, reducing the data risk of a
   * table data profile also reduces the data risk of the constituent column
   * data profiles.
   *
   * @param bool $lowerDataRiskToLow
   */
  public function setLowerDataRiskToLow($lowerDataRiskToLow)
  {
    $this->lowerDataRiskToLow = $lowerDataRiskToLow;
  }
  /**
   * @return bool
   */
  public function getLowerDataRiskToLow()
  {
    return $this->lowerDataRiskToLow;
  }
  /**
   * The profile generations for which the tag should be attached to resources.
   * If you attach a tag to only new profiles, then if the sensitivity score of
   * a profile subsequently changes, its tag doesn't change. By default, this
   * field includes only new profiles. To include both new and updated profiles
   * for tagging, this field should explicitly include both
   * `PROFILE_GENERATION_NEW` and `PROFILE_GENERATION_UPDATE`.
   *
   * @param string[] $profileGenerationsToTag
   */
  public function setProfileGenerationsToTag($profileGenerationsToTag)
  {
    $this->profileGenerationsToTag = $profileGenerationsToTag;
  }
  /**
   * @return string[]
   */
  public function getProfileGenerationsToTag()
  {
    return $this->profileGenerationsToTag;
  }
  /**
   * The tags to associate with different conditions.
   *
   * @param GooglePrivacyDlpV2TagCondition[] $tagConditions
   */
  public function setTagConditions($tagConditions)
  {
    $this->tagConditions = $tagConditions;
  }
  /**
   * @return GooglePrivacyDlpV2TagCondition[]
   */
  public function getTagConditions()
  {
    return $this->tagConditions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TagResources::class, 'Google_Service_DLP_GooglePrivacyDlpV2TagResources');
