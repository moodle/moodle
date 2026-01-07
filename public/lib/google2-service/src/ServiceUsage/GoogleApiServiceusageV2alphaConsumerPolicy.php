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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV2alphaConsumerPolicy extends \Google\Collection
{
  protected $collection_key = 'enableRules';
  /**
   * Optional. Annotations is an unstructured key-value map stored with a policy
   * that may be set by external tools to store and retrieve arbitrary metadata.
   * They are not queryable and should be preserved when modifying objects.
   * [AIP-128](https://google.aip.dev/128#annotations)
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The time the policy was created. For singleton policies, this
   * is the first touch of the policy.
   *
   * @var string
   */
  public $createTime;
  protected $enableRulesType = GoogleApiServiceusageV2alphaEnableRule::class;
  protected $enableRulesDataType = 'array';
  /**
   * Output only. An opaque tag indicating the current version of the policy,
   * used for concurrency control.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The resource name of the policy. Only the `default` policy is
   * supported: `projects/12345/consumerPolicies/default`,
   * `folders/12345/consumerPolicies/default`,
   * `organizations/12345/consumerPolicies/default`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time the policy was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Annotations is an unstructured key-value map stored with a policy
   * that may be set by external tools to store and retrieve arbitrary metadata.
   * They are not queryable and should be preserved when modifying objects.
   * [AIP-128](https://google.aip.dev/128#annotations)
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The time the policy was created. For singleton policies, this
   * is the first touch of the policy.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Enable rules define usable services, groups, and categories. There can
   * currently be at most one `EnableRule`. This restriction will be lifted in
   * later releases.
   *
   * @param GoogleApiServiceusageV2alphaEnableRule[] $enableRules
   */
  public function setEnableRules($enableRules)
  {
    $this->enableRules = $enableRules;
  }
  /**
   * @return GoogleApiServiceusageV2alphaEnableRule[]
   */
  public function getEnableRules()
  {
    return $this->enableRules;
  }
  /**
   * Output only. An opaque tag indicating the current version of the policy,
   * used for concurrency control.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The resource name of the policy. Only the `default` policy is
   * supported: `projects/12345/consumerPolicies/default`,
   * `folders/12345/consumerPolicies/default`,
   * `organizations/12345/consumerPolicies/default`.
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
   * Output only. The time the policy was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV2alphaConsumerPolicy::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV2alphaConsumerPolicy');
