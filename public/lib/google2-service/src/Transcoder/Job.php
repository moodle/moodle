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

namespace Google\Service\Transcoder;

class Job extends \Google\Model
{
  /**
   * The job processing mode is not specified.
   */
  public const MODE_PROCESSING_MODE_UNSPECIFIED = 'PROCESSING_MODE_UNSPECIFIED';
  /**
   * The job processing mode is interactive mode. Interactive job will either be
   * ran or rejected if quota does not allow for it.
   */
  public const MODE_PROCESSING_MODE_INTERACTIVE = 'PROCESSING_MODE_INTERACTIVE';
  /**
   * The job processing mode is batch mode. Batch mode allows queuing of jobs.
   */
  public const MODE_PROCESSING_MODE_BATCH = 'PROCESSING_MODE_BATCH';
  /**
   * The optimization strategy is not specified.
   */
  public const OPTIMIZATION_OPTIMIZATION_STRATEGY_UNSPECIFIED = 'OPTIMIZATION_STRATEGY_UNSPECIFIED';
  /**
   * Prioritize job processing speed.
   */
  public const OPTIMIZATION_AUTODETECT = 'AUTODETECT';
  /**
   * Disable all optimizations.
   */
  public const OPTIMIZATION_DISABLED = 'DISABLED';
  /**
   * The processing state is not specified.
   */
  public const STATE_PROCESSING_STATE_UNSPECIFIED = 'PROCESSING_STATE_UNSPECIFIED';
  /**
   * The job is enqueued and will be picked up for processing soon.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The job is being processed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job has been completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The job has failed. For additional information, see [Troubleshooting](https
   * ://cloud.google.com/transcoder/docs/troubleshooting).
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The processing priority of a batch job. This field can only be set for
   * batch mode jobs. The default value is 0. This value cannot be negative.
   * Higher values correspond to higher priorities for the job.
   *
   * @var int
   */
  public $batchModePriority;
  protected $configType = JobConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time the job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time the transcoding finished.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Optional. Insert silence and duplicate frames when timestamp gaps are
   * detected in a given stream.
   *
   * @var bool
   */
  public $fillContentGaps;
  /**
   * Input only. Specify the `input_uri` to populate empty `uri` fields in each
   * element of `Job.config.inputs` or `JobTemplate.config.inputs` when using
   * template. URI of the media. Input files must be at least 5 seconds in
   * duration and stored in Cloud Storage (for example,
   * `gs://bucket/inputs/file.mp4`). See [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
   *
   * @var string
   */
  public $inputUri;
  /**
   * The labels associated with this job. You can use these to organize and
   * group your jobs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The processing mode of the job. The default is
   * `PROCESSING_MODE_INTERACTIVE`.
   *
   * @var string
   */
  public $mode;
  /**
   * The resource name of the job. Format:
   * `projects/{project_number}/locations/{location}/jobs/{job}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The optimization strategy of the job. The default is
   * `AUTODETECT`.
   *
   * @var string
   */
  public $optimization;
  /**
   * Input only. Specify the `output_uri` to populate an empty
   * `Job.config.output.uri` or `JobTemplate.config.output.uri` when using
   * template. URI for the output file(s). For example, `gs://my-
   * bucket/outputs/`. See [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
   *
   * @var string
   */
  public $outputUri;
  /**
   * Output only. The time the transcoding started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current state of the job.
   *
   * @var string
   */
  public $state;
  /**
   * Input only. Specify the `template_id` to use for populating `Job.config`.
   * The default is `preset/web-hd`, which is the only supported preset. User
   * defined JobTemplate: `{job_template_id}`
   *
   * @var string
   */
  public $templateId;
  /**
   * Job time to live value in days, which will be effective after job
   * completion. Job should be deleted automatically after the given TTL. Enter
   * a value between 1 and 90. The default is 30.
   *
   * @var int
   */
  public $ttlAfterCompletionDays;

  /**
   * The processing priority of a batch job. This field can only be set for
   * batch mode jobs. The default value is 0. This value cannot be negative.
   * Higher values correspond to higher priorities for the job.
   *
   * @param int $batchModePriority
   */
  public function setBatchModePriority($batchModePriority)
  {
    $this->batchModePriority = $batchModePriority;
  }
  /**
   * @return int
   */
  public function getBatchModePriority()
  {
    return $this->batchModePriority;
  }
  /**
   * The configuration for this job.
   *
   * @param JobConfig $config
   */
  public function setConfig(JobConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return JobConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time the job was created.
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
   * Output only. The time the transcoding finished.
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
   * Output only. An error object that describes the reason for the failure.
   * This property is always present when ProcessingState is `FAILED`.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Optional. Insert silence and duplicate frames when timestamp gaps are
   * detected in a given stream.
   *
   * @param bool $fillContentGaps
   */
  public function setFillContentGaps($fillContentGaps)
  {
    $this->fillContentGaps = $fillContentGaps;
  }
  /**
   * @return bool
   */
  public function getFillContentGaps()
  {
    return $this->fillContentGaps;
  }
  /**
   * Input only. Specify the `input_uri` to populate empty `uri` fields in each
   * element of `Job.config.inputs` or `JobTemplate.config.inputs` when using
   * template. URI of the media. Input files must be at least 5 seconds in
   * duration and stored in Cloud Storage (for example,
   * `gs://bucket/inputs/file.mp4`). See [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
   *
   * @param string $inputUri
   */
  public function setInputUri($inputUri)
  {
    $this->inputUri = $inputUri;
  }
  /**
   * @return string
   */
  public function getInputUri()
  {
    return $this->inputUri;
  }
  /**
   * The labels associated with this job. You can use these to organize and
   * group your jobs.
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
   * The processing mode of the job. The default is
   * `PROCESSING_MODE_INTERACTIVE`.
   *
   * Accepted values: PROCESSING_MODE_UNSPECIFIED, PROCESSING_MODE_INTERACTIVE,
   * PROCESSING_MODE_BATCH
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The resource name of the job. Format:
   * `projects/{project_number}/locations/{location}/jobs/{job}`
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
   * Optional. The optimization strategy of the job. The default is
   * `AUTODETECT`.
   *
   * Accepted values: OPTIMIZATION_STRATEGY_UNSPECIFIED, AUTODETECT, DISABLED
   *
   * @param self::OPTIMIZATION_* $optimization
   */
  public function setOptimization($optimization)
  {
    $this->optimization = $optimization;
  }
  /**
   * @return self::OPTIMIZATION_*
   */
  public function getOptimization()
  {
    return $this->optimization;
  }
  /**
   * Input only. Specify the `output_uri` to populate an empty
   * `Job.config.output.uri` or `JobTemplate.config.output.uri` when using
   * template. URI for the output file(s). For example, `gs://my-
   * bucket/outputs/`. See [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
   *
   * @param string $outputUri
   */
  public function setOutputUri($outputUri)
  {
    $this->outputUri = $outputUri;
  }
  /**
   * @return string
   */
  public function getOutputUri()
  {
    return $this->outputUri;
  }
  /**
   * Output only. The time the transcoding started.
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
   * Output only. The current state of the job.
   *
   * Accepted values: PROCESSING_STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED,
   * FAILED
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
   * Input only. Specify the `template_id` to use for populating `Job.config`.
   * The default is `preset/web-hd`, which is the only supported preset. User
   * defined JobTemplate: `{job_template_id}`
   *
   * @param string $templateId
   */
  public function setTemplateId($templateId)
  {
    $this->templateId = $templateId;
  }
  /**
   * @return string
   */
  public function getTemplateId()
  {
    return $this->templateId;
  }
  /**
   * Job time to live value in days, which will be effective after job
   * completion. Job should be deleted automatically after the given TTL. Enter
   * a value between 1 and 90. The default is 30.
   *
   * @param int $ttlAfterCompletionDays
   */
  public function setTtlAfterCompletionDays($ttlAfterCompletionDays)
  {
    $this->ttlAfterCompletionDays = $ttlAfterCompletionDays;
  }
  /**
   * @return int
   */
  public function getTtlAfterCompletionDays()
  {
    return $this->ttlAfterCompletionDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_Transcoder_Job');
