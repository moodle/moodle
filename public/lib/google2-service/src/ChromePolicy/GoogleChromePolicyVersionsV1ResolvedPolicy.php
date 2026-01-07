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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1ResolvedPolicy extends \Google\Model
{
  protected $addedSourceKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $addedSourceKeyDataType = '';
  protected $sourceKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $sourceKeyDataType = '';
  protected $targetKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $targetKeyDataType = '';
  protected $valueType = GoogleChromePolicyVersionsV1PolicyValue::class;
  protected $valueDataType = '';

  /**
   * Output only. The added source key establishes at which level an entity was
   * explicitly added for management. This is useful for certain type of
   * policies that are only applied if they are explicitly added for management.
   * For example: apps and networks. An entity can only be deleted from
   * management in an Organizational Unit that it was explicitly added to. If
   * this is not present it means that the policy is managed without the need to
   * explicitly add an entity, for example: standard user or device policies.
   *
   * @param GoogleChromePolicyVersionsV1PolicyTargetKey $addedSourceKey
   */
  public function setAddedSourceKey(GoogleChromePolicyVersionsV1PolicyTargetKey $addedSourceKey)
  {
    $this->addedSourceKey = $addedSourceKey;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyTargetKey
   */
  public function getAddedSourceKey()
  {
    return $this->addedSourceKey;
  }
  /**
   * Output only. The source resource from which this policy value is obtained.
   * May be the same as `targetKey` if the policy is directly modified on the
   * target, otherwise it would be another resource from which the policy gets
   * its value (if applicable). If not present, the source is the default value
   * for the customer.
   *
   * @param GoogleChromePolicyVersionsV1PolicyTargetKey $sourceKey
   */
  public function setSourceKey(GoogleChromePolicyVersionsV1PolicyTargetKey $sourceKey)
  {
    $this->sourceKey = $sourceKey;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyTargetKey
   */
  public function getSourceKey()
  {
    return $this->sourceKey;
  }
  /**
   * Output only. The target resource for which the resolved policy value
   * applies.
   *
   * @param GoogleChromePolicyVersionsV1PolicyTargetKey $targetKey
   */
  public function setTargetKey(GoogleChromePolicyVersionsV1PolicyTargetKey $targetKey)
  {
    $this->targetKey = $targetKey;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyTargetKey
   */
  public function getTargetKey()
  {
    return $this->targetKey;
  }
  /**
   * Output only. The resolved value of the policy.
   *
   * @param GoogleChromePolicyVersionsV1PolicyValue $value
   */
  public function setValue(GoogleChromePolicyVersionsV1PolicyValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1ResolvedPolicy::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1ResolvedPolicy');
