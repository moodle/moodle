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

namespace Google\Service\CloudSearch;

class Trigger extends \Google\Model
{
  /**
   * @var string
   */
  public $actionType;
  /**
   * @var string
   */
  public $batchTimeUs;
  /**
   * @var string
   */
  public $dispatchId;
  /**
   * @var string
   */
  public $dispatcher;
  /**
   * @var string
   */
  public $fireTimeUs;
  protected $jobsettedServerSpecType = JobsettedServerSpec::class;
  protected $jobsettedServerSpecDataType = '';
  /**
   * @var string
   */
  public $key;
  protected $rpcOptionsType = RpcOptions::class;
  protected $rpcOptionsDataType = '';
  /**
   * @var string
   */
  public $sliceFireTimeUs;
  protected $triggerActionType = TriggerAction::class;
  protected $triggerActionDataType = '';
  protected $triggerKeyType = TriggerKey::class;
  protected $triggerKeyDataType = '';

  /**
   * @param string
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return string
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * @param string
   */
  public function setBatchTimeUs($batchTimeUs)
  {
    $this->batchTimeUs = $batchTimeUs;
  }
  /**
   * @return string
   */
  public function getBatchTimeUs()
  {
    return $this->batchTimeUs;
  }
  /**
   * @param string
   */
  public function setDispatchId($dispatchId)
  {
    $this->dispatchId = $dispatchId;
  }
  /**
   * @return string
   */
  public function getDispatchId()
  {
    return $this->dispatchId;
  }
  /**
   * @param string
   */
  public function setDispatcher($dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }
  /**
   * @return string
   */
  public function getDispatcher()
  {
    return $this->dispatcher;
  }
  /**
   * @param string
   */
  public function setFireTimeUs($fireTimeUs)
  {
    $this->fireTimeUs = $fireTimeUs;
  }
  /**
   * @return string
   */
  public function getFireTimeUs()
  {
    return $this->fireTimeUs;
  }
  /**
   * @param JobsettedServerSpec
   */
  public function setJobsettedServerSpec(JobsettedServerSpec $jobsettedServerSpec)
  {
    $this->jobsettedServerSpec = $jobsettedServerSpec;
  }
  /**
   * @return JobsettedServerSpec
   */
  public function getJobsettedServerSpec()
  {
    return $this->jobsettedServerSpec;
  }
  /**
   * @param string
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * @param RpcOptions
   */
  public function setRpcOptions(RpcOptions $rpcOptions)
  {
    $this->rpcOptions = $rpcOptions;
  }
  /**
   * @return RpcOptions
   */
  public function getRpcOptions()
  {
    return $this->rpcOptions;
  }
  /**
   * @param string
   */
  public function setSliceFireTimeUs($sliceFireTimeUs)
  {
    $this->sliceFireTimeUs = $sliceFireTimeUs;
  }
  /**
   * @return string
   */
  public function getSliceFireTimeUs()
  {
    return $this->sliceFireTimeUs;
  }
  /**
   * @param TriggerAction
   */
  public function setTriggerAction(TriggerAction $triggerAction)
  {
    $this->triggerAction = $triggerAction;
  }
  /**
   * @return TriggerAction
   */
  public function getTriggerAction()
  {
    return $this->triggerAction;
  }
  /**
   * @param TriggerKey
   */
  public function setTriggerKey(TriggerKey $triggerKey)
  {
    $this->triggerKey = $triggerKey;
  }
  /**
   * @return TriggerKey
   */
  public function getTriggerKey()
  {
    return $this->triggerKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Trigger::class, 'Google_Service_CloudSearch_Trigger');
