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

class ConsumerPolicy extends \Google\Collection
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
  protected $enableRulesType = EnableRule::class;
  protected $enableRulesDataType = 'array';
  /**
   * An opaque tag indicating the current version of the policy, used for
   * concurrency control.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The resource name of the policy. We only allow consumer policy
   * name as `default` for now: `projects/12345/consumerPolicies/default`,
   * `folders/12345/consumerPolicies/default`,
   * `organizations/12345/consumerPolicies/default`.
   *
   * @var string
   */
  public $name;
  /**
   * The last-modified time.
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
   * Enable rules define usable services and service groups.
   *
   * @param EnableRule[] $enableRules
   */
  public function setEnableRules($enableRules)
  {
    $this->enableRules = $enableRules;
  }
  /**
   * @return EnableRule[]
   */
  public function getEnableRules()
  {
    return $this->enableRules;
  }
  /**
   * An opaque tag indicating the current version of the policy, used for
   * concurrency control.
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
   * Output only. The resource name of the policy. We only allow consumer policy
   * name as `default` for now: `projects/12345/consumerPolicies/default`,
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
   * The last-modified time.
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
class_alias(ConsumerPolicy::class, 'Google_Service_ServiceUsage_ConsumerPolicy');
