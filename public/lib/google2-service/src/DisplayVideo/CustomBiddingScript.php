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

class CustomBiddingScript extends \Google\Collection
{
  /**
   * The script state is not specified or is unknown in this version.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The script has been accepted for scoring impressions.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The script has been rejected by backend pipelines. It may have errors.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * The script is being processed for backend pipelines.
   */
  public const STATE_PENDING = 'PENDING';
  protected $collection_key = 'errors';
  /**
   * Output only. Whether the script is currently being used for scoring by the
   * parent algorithm.
   *
   * @var bool
   */
  public $active;
  /**
   * Output only. The time when the script was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The unique ID of the custom bidding algorithm the script
   * belongs to.
   *
   * @var string
   */
  public $customBiddingAlgorithmId;
  /**
   * Output only. The unique ID of the custom bidding script.
   *
   * @var string
   */
  public $customBiddingScriptId;
  protected $errorsType = ScriptError::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. The resource name of the custom bidding script.
   *
   * @var string
   */
  public $name;
  protected $scriptType = CustomBiddingScriptRef::class;
  protected $scriptDataType = '';
  /**
   * Output only. The state of the custom bidding script.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Whether the script is currently being used for scoring by the
   * parent algorithm.
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
   * Output only. The time when the script was created.
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
   * Output only. The unique ID of the custom bidding algorithm the script
   * belongs to.
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
   * Output only. The unique ID of the custom bidding script.
   *
   * @param string $customBiddingScriptId
   */
  public function setCustomBiddingScriptId($customBiddingScriptId)
  {
    $this->customBiddingScriptId = $customBiddingScriptId;
  }
  /**
   * @return string
   */
  public function getCustomBiddingScriptId()
  {
    return $this->customBiddingScriptId;
  }
  /**
   * Output only. Error details of a rejected custom bidding script. This field
   * will only be populated when state is REJECTED.
   *
   * @param ScriptError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ScriptError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. The resource name of the custom bidding script.
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
   * The reference to the uploaded script file.
   *
   * @param CustomBiddingScriptRef $script
   */
  public function setScript(CustomBiddingScriptRef $script)
  {
    $this->script = $script;
  }
  /**
   * @return CustomBiddingScriptRef
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * Output only. The state of the custom bidding script.
   *
   * Accepted values: STATE_UNSPECIFIED, ACCEPTED, REJECTED, PENDING
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
class_alias(CustomBiddingScript::class, 'Google_Service_DisplayVideo_CustomBiddingScript');
