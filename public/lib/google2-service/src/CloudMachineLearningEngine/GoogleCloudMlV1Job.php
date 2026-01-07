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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1Job extends \Google\Model
{
  /**
   * The job state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job has been just created and processing has not yet begun.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * The service is preparing to run the job.
   */
  public const STATE_PREPARING = 'PREPARING';
  /**
   * The job is in progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The job failed. `error_message` should contain the details of the failure.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job is being cancelled. `error_message` should describe the reason for
   * the cancellation.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The job has been cancelled. `error_message` should describe the reason for
   * the cancellation.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Output only. When the job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. When the job processing was completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. The details of a failure or a cancellation.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a job from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform job updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetJob`, and systems are expected to put
   * that etag in the request to `UpdateJob` to ensure that their change will be
   * applied to the same version of the job.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. The user-specified id of the job.
   *
   * @var string
   */
  public $jobId;
  /**
   * Output only. It's only effect when the job is in QUEUED state. If it's
   * positive, it indicates the job's position in the job scheduler. It's 0 when
   * the job is already scheduled.
   *
   * @var string
   */
  public $jobPosition;
  /**
   * Optional. One or more labels that you can add, to organize your jobs. Each
   * label is a key-value pair, where both the key and the value are arbitrary
   * strings that you supply. For more information, see the documentation on
   * using labels.
   *
   * @var string[]
   */
  public $labels;
  protected $predictionInputType = GoogleCloudMlV1PredictionInput::class;
  protected $predictionInputDataType = '';
  protected $predictionOutputType = GoogleCloudMlV1PredictionOutput::class;
  protected $predictionOutputDataType = '';
  /**
   * Output only. When the job processing was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of a job.
   *
   * @var string
   */
  public $state;
  protected $trainingInputType = GoogleCloudMlV1TrainingInput::class;
  protected $trainingInputDataType = '';
  protected $trainingOutputType = GoogleCloudMlV1TrainingOutput::class;
  protected $trainingOutputDataType = '';

  /**
   * Output only. When the job was created.
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
   * Output only. When the job processing was completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The details of a failure or a cancellation.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a job from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform job updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetJob`, and systems are expected to put
   * that etag in the request to `UpdateJob` to ensure that their change will be
   * applied to the same version of the job.
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
   * Required. The user-specified id of the job.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * Output only. It's only effect when the job is in QUEUED state. If it's
   * positive, it indicates the job's position in the job scheduler. It's 0 when
   * the job is already scheduled.
   *
   * @param string $jobPosition
   */
  public function setJobPosition($jobPosition)
  {
    $this->jobPosition = $jobPosition;
  }
  /**
   * @return string
   */
  public function getJobPosition()
  {
    return $this->jobPosition;
  }
  /**
   * Optional. One or more labels that you can add, to organize your jobs. Each
   * label is a key-value pair, where both the key and the value are arbitrary
   * strings that you supply. For more information, see the documentation on
   * using labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Input parameters to create a prediction job.
   *
   * @param GoogleCloudMlV1PredictionInput $predictionInput
   */
  public function setPredictionInput(GoogleCloudMlV1PredictionInput $predictionInput)
  {
    $this->predictionInput = $predictionInput;
  }
  /**
   * @return GoogleCloudMlV1PredictionInput
   */
  public function getPredictionInput()
  {
    return $this->predictionInput;
  }
  /**
   * The current prediction job result.
   *
   * @param GoogleCloudMlV1PredictionOutput $predictionOutput
   */
  public function setPredictionOutput(GoogleCloudMlV1PredictionOutput $predictionOutput)
  {
    $this->predictionOutput = $predictionOutput;
  }
  /**
   * @return GoogleCloudMlV1PredictionOutput
   */
  public function getPredictionOutput()
  {
    return $this->predictionOutput;
  }
  /**
   * Output only. When the job processing was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The detailed state of a job.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, PREPARING, RUNNING, SUCCEEDED,
   * FAILED, CANCELLING, CANCELLED
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
  /**
   * Input parameters to create a training job.
   *
   * @param GoogleCloudMlV1TrainingInput $trainingInput
   */
  public function setTrainingInput(GoogleCloudMlV1TrainingInput $trainingInput)
  {
    $this->trainingInput = $trainingInput;
  }
  /**
   * @return GoogleCloudMlV1TrainingInput
   */
  public function getTrainingInput()
  {
    return $this->trainingInput;
  }
  /**
   * The current training job result.
   *
   * @param GoogleCloudMlV1TrainingOutput $trainingOutput
   */
  public function setTrainingOutput(GoogleCloudMlV1TrainingOutput $trainingOutput)
  {
    $this->trainingOutput = $trainingOutput;
  }
  /**
   * @return GoogleCloudMlV1TrainingOutput
   */
  public function getTrainingOutput()
  {
    return $this->trainingOutput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Job::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Job');
