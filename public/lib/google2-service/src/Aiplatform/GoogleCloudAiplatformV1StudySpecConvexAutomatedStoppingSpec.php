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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec extends \Google\Model
{
  /**
   * The hyper-parameter name used in the tuning job that stands for learning
   * rate. Leave it blank if learning rate is not in a parameter in tuning. The
   * learning_rate is used to estimate the objective value of the ongoing trial.
   *
   * @var string
   */
  public $learningRateParameterName;
  /**
   * Steps used in predicting the final objective for early stopped trials. In
   * general, it's set to be the same as the defined steps in training / tuning.
   * If not defined, it will learn it from the completed trials. When use_steps
   * is false, this field is set to the maximum elapsed seconds.
   *
   * @var string
   */
  public $maxStepCount;
  /**
   * The minimal number of measurements in a Trial. Early-stopping checks will
   * not trigger if less than min_measurement_count+1 completed trials or
   * pending trials with less than min_measurement_count measurements. If not
   * defined, the default value is 5.
   *
   * @var string
   */
  public $minMeasurementCount;
  /**
   * Minimum number of steps for a trial to complete. Trials which do not have a
   * measurement with step_count > min_step_count won't be considered for early
   * stopping. It's ok to set it to 0, and a trial can be early stopped at any
   * stage. By default, min_step_count is set to be one-tenth of the
   * max_step_count. When use_elapsed_duration is true, this field is set to the
   * minimum elapsed seconds.
   *
   * @var string
   */
  public $minStepCount;
  /**
   * ConvexAutomatedStoppingSpec by default only updates the trials that needs
   * to be early stopped using a newly trained auto-regressive model. When this
   * flag is set to True, all stopped trials from the beginning are potentially
   * updated in terms of their `final_measurement`. Also, note that the training
   * logic of autoregressive models is different in this case. Enabling this
   * option has shown better results and this may be the default option in the
   * future.
   *
   * @var bool
   */
  public $updateAllStoppedTrials;
  /**
   * This bool determines whether or not the rule is applied based on
   * elapsed_secs or steps. If use_elapsed_duration==false, the early stopping
   * decision is made according to the predicted objective values according to
   * the target steps. If use_elapsed_duration==true, elapsed_secs is used
   * instead of steps. Also, in this case, the parameters max_num_steps and
   * min_num_steps are overloaded to contain max_elapsed_seconds and
   * min_elapsed_seconds.
   *
   * @var bool
   */
  public $useElapsedDuration;

  /**
   * The hyper-parameter name used in the tuning job that stands for learning
   * rate. Leave it blank if learning rate is not in a parameter in tuning. The
   * learning_rate is used to estimate the objective value of the ongoing trial.
   *
   * @param string $learningRateParameterName
   */
  public function setLearningRateParameterName($learningRateParameterName)
  {
    $this->learningRateParameterName = $learningRateParameterName;
  }
  /**
   * @return string
   */
  public function getLearningRateParameterName()
  {
    return $this->learningRateParameterName;
  }
  /**
   * Steps used in predicting the final objective for early stopped trials. In
   * general, it's set to be the same as the defined steps in training / tuning.
   * If not defined, it will learn it from the completed trials. When use_steps
   * is false, this field is set to the maximum elapsed seconds.
   *
   * @param string $maxStepCount
   */
  public function setMaxStepCount($maxStepCount)
  {
    $this->maxStepCount = $maxStepCount;
  }
  /**
   * @return string
   */
  public function getMaxStepCount()
  {
    return $this->maxStepCount;
  }
  /**
   * The minimal number of measurements in a Trial. Early-stopping checks will
   * not trigger if less than min_measurement_count+1 completed trials or
   * pending trials with less than min_measurement_count measurements. If not
   * defined, the default value is 5.
   *
   * @param string $minMeasurementCount
   */
  public function setMinMeasurementCount($minMeasurementCount)
  {
    $this->minMeasurementCount = $minMeasurementCount;
  }
  /**
   * @return string
   */
  public function getMinMeasurementCount()
  {
    return $this->minMeasurementCount;
  }
  /**
   * Minimum number of steps for a trial to complete. Trials which do not have a
   * measurement with step_count > min_step_count won't be considered for early
   * stopping. It's ok to set it to 0, and a trial can be early stopped at any
   * stage. By default, min_step_count is set to be one-tenth of the
   * max_step_count. When use_elapsed_duration is true, this field is set to the
   * minimum elapsed seconds.
   *
   * @param string $minStepCount
   */
  public function setMinStepCount($minStepCount)
  {
    $this->minStepCount = $minStepCount;
  }
  /**
   * @return string
   */
  public function getMinStepCount()
  {
    return $this->minStepCount;
  }
  /**
   * ConvexAutomatedStoppingSpec by default only updates the trials that needs
   * to be early stopped using a newly trained auto-regressive model. When this
   * flag is set to True, all stopped trials from the beginning are potentially
   * updated in terms of their `final_measurement`. Also, note that the training
   * logic of autoregressive models is different in this case. Enabling this
   * option has shown better results and this may be the default option in the
   * future.
   *
   * @param bool $updateAllStoppedTrials
   */
  public function setUpdateAllStoppedTrials($updateAllStoppedTrials)
  {
    $this->updateAllStoppedTrials = $updateAllStoppedTrials;
  }
  /**
   * @return bool
   */
  public function getUpdateAllStoppedTrials()
  {
    return $this->updateAllStoppedTrials;
  }
  /**
   * This bool determines whether or not the rule is applied based on
   * elapsed_secs or steps. If use_elapsed_duration==false, the early stopping
   * decision is made according to the predicted objective values according to
   * the target steps. If use_elapsed_duration==true, elapsed_secs is used
   * instead of steps. Also, in this case, the parameters max_num_steps and
   * min_num_steps are overloaded to contain max_elapsed_seconds and
   * min_elapsed_seconds.
   *
   * @param bool $useElapsedDuration
   */
  public function setUseElapsedDuration($useElapsedDuration)
  {
    $this->useElapsedDuration = $useElapsedDuration;
  }
  /**
   * @return bool
   */
  public function getUseElapsedDuration()
  {
    return $this->useElapsedDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecConvexAutomatedStoppingSpec');
