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

namespace Google\Service\DisplayVideo;

class CustomBiddingAlgorithmRules extends \Google\Model
{
  /**
   * The rules state are unspecified or unknown in this version.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The rules have been accepted for scoring impressions.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The rules have been rejected by backend pipelines. They may have errors.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * Output only. Whether the rules resource is currently being used for scoring
   * by the parent algorithm.
   *
   * @var bool
   */
  public $active;
  /**
   * Output only. The time when the rules resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The unique ID of the custom bidding algorithm that the rules
   * resource belongs to.
   *
   * @var string
   */
  public $customBiddingAlgorithmId;
  /**
   * Output only. The unique ID of the rules resource.
   *
   * @var string
   */
  public $customBiddingAlgorithmRulesId;
  protected $errorType = CustomBiddingAlgorithmRulesError::class;
  protected $errorDataType = '';
  /**
   * Output only. The resource name of the rules resource.
   *
   * @var string
   */
  public $name;
  protected $rulesType = CustomBiddingAlgorithmRulesRef::class;
  protected $rulesDataType = '';
  /**
   * Output only. The state of the rules resource.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Whether the rules resource is currently being used for scoring
   * by the parent algorithm.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Output only. The time when the rules resource was created.
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
   * Output only. The unique ID of the custom bidding algorithm that the rules
   * resource belongs to.
   *
   * @param string $customBiddingAlgorithmId
   */
  public function setCustomBiddingAlgorithmId($customBiddingAlgorithmId)
  {
    $this->customBiddingAlgorithmId = $customBiddingAlgorithmId;
  }
  /**
   * @return string
   */
  public function getCustomBiddingAlgorithmId()
  {
    return $this->customBiddingAlgorithmId;
  }
  /**
   * Output only. The unique ID of the rules resource.
   *
   * @param string $customBiddingAlgorithmRulesId
   */
  public function setCustomBiddingAlgorithmRulesId($customBiddingAlgorithmRulesId)
  {
    $this->customBiddingAlgorithmRulesId = $customBiddingAlgorithmRulesId;
  }
  /**
   * @return string
   */
  public function getCustomBiddingAlgorithmRulesId()
  {
    return $this->customBiddingAlgorithmRulesId;
  }
  /**
   * Output only. Error code of the rejected rules resource. This field will
   * only be populated when the state is `REJECTED`.
   *
   * @param CustomBiddingAlgorithmRulesError $error
   */
  public function setError(CustomBiddingAlgorithmRulesError $error)
  {
    $this->error = $error;
  }
  /**
   * @return CustomBiddingAlgorithmRulesError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The resource name of the rules resource.
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
   * Required. Immutable. The reference to the uploaded AlgorithmRules file.
   *
   * @param CustomBiddingAlgorithmRulesRef $rules
   */
  public function setRules(CustomBiddingAlgorithmRulesRef $rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return CustomBiddingAlgorithmRulesRef
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. The state of the rules resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACCEPTED, REJECTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomBiddingAlgorithmRules::class, 'Google_Service_DisplayVideo_CustomBiddingAlgorithmRules');
