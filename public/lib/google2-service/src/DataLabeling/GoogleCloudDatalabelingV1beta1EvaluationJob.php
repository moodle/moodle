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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1EvaluationJob extends \Google\Collection
{
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is scheduled to run at the configured interval. You can pause or
   * delete the job. When the job is in this state, it samples prediction input
   * and output from your model version into your BigQuery table as predictions
   * occur.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * The job is currently running. When the job runs, Data Labeling Service does
   * several things: 1. If you have configured your job to use Data Labeling
   * Service for ground truth labeling, the service creates a Dataset and a
   * labeling task for all data sampled since the last time the job ran. Human
   * labelers provide ground truth labels for your data. Human labeling may take
   * hours, or even days, depending on how much data has been sampled. The job
   * remains in the `RUNNING` state during this time, and it can even be running
   * multiple times in parallel if it gets triggered again (for example 24 hours
   * later) before the earlier run has completed. When human labelers have
   * finished labeling the data, the next step occurs. If you have configured
   * your job to provide your own ground truth labels, Data Labeling Service
   * still creates a Dataset for newly sampled data, but it expects that you
   * have already added ground truth labels to the BigQuery table by this time.
   * The next step occurs immediately. 2. Data Labeling Service creates an
   * Evaluation by comparing your model version's predictions with the ground
   * truth labels. If the job remains in this state for a long time, it
   * continues to sample prediction data into your BigQuery table and will run
   * again at the next interval, even if it causes the job to run multiple times
   * in parallel.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job is not sampling prediction input and output into your BigQuery
   * table and it will not run according to its schedule. You can resume the
   * job.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The job has this state right before it is deleted.
   */
  public const STATE_STOPPED = 'STOPPED';
  protected $collection_key = 'attempts';
  /**
   * Required. Name of the AnnotationSpecSet describing all the labels that your
   * machine learning model outputs. You must create this resource before you
   * create an evaluation job and provide its name in the following format:
   * "projects/{project_id}/annotationSpecSets/{annotation_spec_set_id}"
   *
   * @var string
   */
  public $annotationSpecSet;
  protected $attemptsType = GoogleCloudDatalabelingV1beta1Attempt::class;
  protected $attemptsDataType = 'array';
  /**
   * Output only. Timestamp of when this evaluation job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Description of the job. The description can be up to 25,000
   * characters long.
   *
   * @var string
   */
  public $description;
  protected $evaluationJobConfigType = GoogleCloudDatalabelingV1beta1EvaluationJobConfig::class;
  protected $evaluationJobConfigDataType = '';
  /**
   * Required. Whether you want Data Labeling Service to provide ground truth
   * labels for prediction input. If you want the service to assign human
   * labelers to annotate your data, set this to `true`. If you want to provide
   * your own ground truth labels in the evaluation job's BigQuery table, set
   * this to `false`.
   *
   * @var bool
   */
  public $labelMissingGroundTruth;
  /**
   * Required. The [AI Platform Prediction model version](/ml-
   * engine/docs/prediction-overview) to be evaluated. Prediction input and
   * output is sampled from this model version. When creating an evaluation job,
   * specify the model version in the following format:
   * "projects/{project_id}/models/{model_name}/versions/{version_name}" There
   * can only be one evaluation job per model version.
   *
   * @var string
   */
  public $modelVersion;
  /**
   * Output only. After you create a job, Data Labeling Service assigns a name
   * to the job with the following format:
   * "projects/{project_id}/evaluationJobs/ {evaluation_job_id}"
   *
   * @var string
   */
  public $name;
  /**
   * Required. Describes the interval at which the job runs. This interval must
   * be at least 1 day, and it is rounded to the nearest day. For example, if
   * you specify a 50-hour interval, the job runs every 2 days. You can provide
   * the schedule in [crontab format](/scheduler/docs/configuring/cron-job-
   * schedules) or in an [English-like
   * format](/appengine/docs/standard/python/config/cronref#schedule_format).
   * Regardless of what you specify, the job will run at 10:00 AM UTC. Only the
   * interval from this schedule is used, not the specific time of day.
   *
   * @var string
   */
  public $schedule;
  /**
   * Output only. Describes the current state of the job.
   *
   * @var string
   */
  public $state;

  /**
   * Required. Name of the AnnotationSpecSet describing all the labels that your
   * machine learning model outputs. You must create this resource before you
   * create an evaluation job and provide its name in the following format:
   * "projects/{project_id}/annotationSpecSets/{annotation_spec_set_id}"
   *
   * @param string $annotationSpecSet
   */
  public function setAnnotationSpecSet($annotationSpecSet)
  {
    $this->annotationSpecSet = $annotationSpecSet;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecSet()
  {
    return $this->annotationSpecSet;
  }
  /**
   * Output only. Every time the evaluation job runs and an error occurs, the
   * failed attempt is appended to this array.
   *
   * @param GoogleCloudDatalabelingV1beta1Attempt[] $attempts
   */
  public function setAttempts($attempts)
  {
    $this->attempts = $attempts;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1Attempt[]
   */
  public function getAttempts()
  {
    return $this->attempts;
  }
  /**
   * Output only. Timestamp of when this evaluation job was created.
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
   * Required. Description of the job. The description can be up to 25,000
   * characters long.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Configuration details for the evaluation job.
   *
   * @param GoogleCloudDatalabelingV1beta1EvaluationJobConfig $evaluationJobConfig
   */
  public function setEvaluationJobConfig(GoogleCloudDatalabelingV1beta1EvaluationJobConfig $evaluationJobConfig)
  {
    $this->evaluationJobConfig = $evaluationJobConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1EvaluationJobConfig
   */
  public function getEvaluationJobConfig()
  {
    return $this->evaluationJobConfig;
  }
  /**
   * Required. Whether you want Data Labeling Service to provide ground truth
   * labels for prediction input. If you want the service to assign human
   * labelers to annotate your data, set this to `true`. If you want to provide
   * your own ground truth labels in the evaluation job's BigQuery table, set
   * this to `false`.
   *
   * @param bool $labelMissingGroundTruth
   */
  public function setLabelMissingGroundTruth($labelMissingGroundTruth)
  {
    $this->labelMissingGroundTruth = $labelMissingGroundTruth;
  }
  /**
   * @return bool
   */
  public function getLabelMissingGroundTruth()
  {
    return $this->labelMissingGroundTruth;
  }
  /**
   * Required. The [AI Platform Prediction model version](/ml-
   * engine/docs/prediction-overview) to be evaluated. Prediction input and
   * output is sampled from this model version. When creating an evaluation job,
   * specify the model version in the following format:
   * "projects/{project_id}/models/{model_name}/versions/{version_name}" There
   * can only be one evaluation job per model version.
   *
   * @param string $modelVersion
   */
  public function setModelVersion($modelVersion)
  {
    $this->modelVersion = $modelVersion;
  }
  /**
   * @return string
   */
  public function getModelVersion()
  {
    return $this->modelVersion;
  }
  /**
   * Output only. After you create a job, Data Labeling Service assigns a name
   * to the job with the following format:
   * "projects/{project_id}/evaluationJobs/ {evaluation_job_id}"
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
   * Required. Describes the interval at which the job runs. This interval must
   * be at least 1 day, and it is rounded to the nearest day. For example, if
   * you specify a 50-hour interval, the job runs every 2 days. You can provide
   * the schedule in [crontab format](/scheduler/docs/configuring/cron-job-
   * schedules) or in an [English-like
   * format](/appengine/docs/standard/python/config/cronref#schedule_format).
   * Regardless of what you specify, the job will run at 10:00 AM UTC. Only the
   * interval from this schedule is used, not the specific time of day.
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Output only. Describes the current state of the job.
   *
   * Accepted values: STATE_UNSPECIFIED, SCHEDULED, RUNNING, PAUSED, STOPPED
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
class_alias(GoogleCloudDatalabelingV1beta1EvaluationJob::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1EvaluationJob');
