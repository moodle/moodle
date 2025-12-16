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

namespace Google\Service\Dataproc;

class OrderedJob extends \Google\Collection
{
  protected $collection_key = 'prerequisiteStepIds';
  protected $flinkJobType = FlinkJob::class;
  protected $flinkJobDataType = '';
  protected $hadoopJobType = HadoopJob::class;
  protected $hadoopJobDataType = '';
  protected $hiveJobType = HiveJob::class;
  protected $hiveJobDataType = '';
  /**
   * Optional. The labels to associate with this job.Label keys must be between
   * 1 and 63 characters long, and must conform to the following regular
   * expression: \p{Ll}\p{Lo}{0,62}Label values must be between 1 and 63
   * characters long, and must conform to the following regular expression:
   * \p{Ll}\p{Lo}\p{N}_-{0,63}No more than 32 labels can be associated with a
   * given job.
   *
   * @var string[]
   */
  public $labels;
  protected $pigJobType = PigJob::class;
  protected $pigJobDataType = '';
  /**
   * Optional. The optional list of prerequisite job step_ids. If not specified,
   * the job will start at the beginning of workflow.
   *
   * @var string[]
   */
  public $prerequisiteStepIds;
  protected $prestoJobType = PrestoJob::class;
  protected $prestoJobDataType = '';
  protected $pysparkJobType = PySparkJob::class;
  protected $pysparkJobDataType = '';
  protected $schedulingType = JobScheduling::class;
  protected $schedulingDataType = '';
  protected $sparkJobType = SparkJob::class;
  protected $sparkJobDataType = '';
  protected $sparkRJobType = SparkRJob::class;
  protected $sparkRJobDataType = '';
  protected $sparkSqlJobType = SparkSqlJob::class;
  protected $sparkSqlJobDataType = '';
  /**
   * Required. The step id. The id must be unique among all jobs within the
   * template.The step id is used as prefix for job id, as job goog-dataproc-
   * workflow-step-id label, and in prerequisiteStepIds field from other
   * steps.The id must contain only letters (a-z, A-Z), numbers (0-9),
   * underscores (_), and hyphens (-). Cannot begin or end with underscore or
   * hyphen. Must consist of between 3 and 50 characters.
   *
   * @var string
   */
  public $stepId;
  protected $trinoJobType = TrinoJob::class;
  protected $trinoJobDataType = '';

  /**
   * Optional. Job is a Flink job.
   *
   * @param FlinkJob $flinkJob
   */
  public function setFlinkJob(FlinkJob $flinkJob)
  {
    $this->flinkJob = $flinkJob;
  }
  /**
   * @return FlinkJob
   */
  public function getFlinkJob()
  {
    return $this->flinkJob;
  }
  /**
   * Optional. Job is a Hadoop job.
   *
   * @param HadoopJob $hadoopJob
   */
  public function setHadoopJob(HadoopJob $hadoopJob)
  {
    $this->hadoopJob = $hadoopJob;
  }
  /**
   * @return HadoopJob
   */
  public function getHadoopJob()
  {
    return $this->hadoopJob;
  }
  /**
   * Optional. Job is a Hive job.
   *
   * @param HiveJob $hiveJob
   */
  public function setHiveJob(HiveJob $hiveJob)
  {
    $this->hiveJob = $hiveJob;
  }
  /**
   * @return HiveJob
   */
  public function getHiveJob()
  {
    return $this->hiveJob;
  }
  /**
   * Optional. The labels to associate with this job.Label keys must be between
   * 1 and 63 characters long, and must conform to the following regular
   * expression: \p{Ll}\p{Lo}{0,62}Label values must be between 1 and 63
   * characters long, and must conform to the following regular expression:
   * \p{Ll}\p{Lo}\p{N}_-{0,63}No more than 32 labels can be associated with a
   * given job.
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
   * Optional. Job is a Pig job.
   *
   * @param PigJob $pigJob
   */
  public function setPigJob(PigJob $pigJob)
  {
    $this->pigJob = $pigJob;
  }
  /**
   * @return PigJob
   */
  public function getPigJob()
  {
    return $this->pigJob;
  }
  /**
   * Optional. The optional list of prerequisite job step_ids. If not specified,
   * the job will start at the beginning of workflow.
   *
   * @param string[] $prerequisiteStepIds
   */
  public function setPrerequisiteStepIds($prerequisiteStepIds)
  {
    $this->prerequisiteStepIds = $prerequisiteStepIds;
  }
  /**
   * @return string[]
   */
  public function getPrerequisiteStepIds()
  {
    return $this->prerequisiteStepIds;
  }
  /**
   * Optional. Job is a Presto job.
   *
   * @param PrestoJob $prestoJob
   */
  public function setPrestoJob(PrestoJob $prestoJob)
  {
    $this->prestoJob = $prestoJob;
  }
  /**
   * @return PrestoJob
   */
  public function getPrestoJob()
  {
    return $this->prestoJob;
  }
  /**
   * Optional. Job is a PySpark job.
   *
   * @param PySparkJob $pysparkJob
   */
  public function setPysparkJob(PySparkJob $pysparkJob)
  {
    $this->pysparkJob = $pysparkJob;
  }
  /**
   * @return PySparkJob
   */
  public function getPysparkJob()
  {
    return $this->pysparkJob;
  }
  /**
   * Optional. Job scheduling configuration.
   *
   * @param JobScheduling $scheduling
   */
  public function setScheduling(JobScheduling $scheduling)
  {
    $this->scheduling = $scheduling;
  }
  /**
   * @return JobScheduling
   */
  public function getScheduling()
  {
    return $this->scheduling;
  }
  /**
   * Optional. Job is a Spark job.
   *
   * @param SparkJob $sparkJob
   */
  public function setSparkJob(SparkJob $sparkJob)
  {
    $this->sparkJob = $sparkJob;
  }
  /**
   * @return SparkJob
   */
  public function getSparkJob()
  {
    return $this->sparkJob;
  }
  /**
   * Optional. Job is a SparkR job.
   *
   * @param SparkRJob $sparkRJob
   */
  public function setSparkRJob(SparkRJob $sparkRJob)
  {
    $this->sparkRJob = $sparkRJob;
  }
  /**
   * @return SparkRJob
   */
  public function getSparkRJob()
  {
    return $this->sparkRJob;
  }
  /**
   * Optional. Job is a SparkSql job.
   *
   * @param SparkSqlJob $sparkSqlJob
   */
  public function setSparkSqlJob(SparkSqlJob $sparkSqlJob)
  {
    $this->sparkSqlJob = $sparkSqlJob;
  }
  /**
   * @return SparkSqlJob
   */
  public function getSparkSqlJob()
  {
    return $this->sparkSqlJob;
  }
  /**
   * Required. The step id. The id must be unique among all jobs within the
   * template.The step id is used as prefix for job id, as job goog-dataproc-
   * workflow-step-id label, and in prerequisiteStepIds field from other
   * steps.The id must contain only letters (a-z, A-Z), numbers (0-9),
   * underscores (_), and hyphens (-). Cannot begin or end with underscore or
   * hyphen. Must consist of between 3 and 50 characters.
   *
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
  /**
   * Optional. Job is a Trino job.
   *
   * @param TrinoJob $trinoJob
   */
  public function setTrinoJob(TrinoJob $trinoJob)
  {
    $this->trinoJob = $trinoJob;
  }
  /**
   * @return TrinoJob
   */
  public function getTrinoJob()
  {
    return $this->trinoJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderedJob::class, 'Google_Service_Dataproc_OrderedJob');
