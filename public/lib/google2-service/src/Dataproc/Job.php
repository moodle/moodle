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

class Job extends \Google\Collection
{
  protected $collection_key = 'yarnApplications';
  /**
   * Output only. Indicates whether the job is completed. If the value is false,
   * the job is still in progress. If true, the job is completed, and
   * status.state field will indicate if it was successful, failed, or
   * cancelled.
   *
   * @var bool
   */
  public $done;
  /**
   * Output only. If present, the location of miscellaneous control files which
   * can be used as part of job setup and handling. If not present, control
   * files might be placed in the same location as driver_output_uri.
   *
   * @var string
   */
  public $driverControlFilesUri;
  /**
   * Output only. A URI pointing to the location of the stdout of the job's
   * driver program.
   *
   * @var string
   */
  public $driverOutputResourceUri;
  protected $driverSchedulingConfigType = DriverSchedulingConfig::class;
  protected $driverSchedulingConfigDataType = '';
  protected $flinkJobType = FlinkJob::class;
  protected $flinkJobDataType = '';
  protected $hadoopJobType = HadoopJob::class;
  protected $hadoopJobDataType = '';
  protected $hiveJobType = HiveJob::class;
  protected $hiveJobDataType = '';
  /**
   * Output only. A UUID that uniquely identifies a job within the project over
   * time. This is in contrast to a user-settable reference.job_id that might be
   * reused over time.
   *
   * @var string
   */
  public $jobUuid;
  /**
   * Optional. The labels to associate with this job. Label keys must contain 1
   * to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values can be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a job.
   *
   * @var string[]
   */
  public $labels;
  protected $pigJobType = PigJob::class;
  protected $pigJobDataType = '';
  protected $placementType = JobPlacement::class;
  protected $placementDataType = '';
  protected $prestoJobType = PrestoJob::class;
  protected $prestoJobDataType = '';
  protected $pysparkJobType = PySparkJob::class;
  protected $pysparkJobDataType = '';
  protected $referenceType = JobReference::class;
  protected $referenceDataType = '';
  protected $schedulingType = JobScheduling::class;
  protected $schedulingDataType = '';
  protected $sparkJobType = SparkJob::class;
  protected $sparkJobDataType = '';
  protected $sparkRJobType = SparkRJob::class;
  protected $sparkRJobDataType = '';
  protected $sparkSqlJobType = SparkSqlJob::class;
  protected $sparkSqlJobDataType = '';
  protected $statusType = JobStatus::class;
  protected $statusDataType = '';
  protected $statusHistoryType = JobStatus::class;
  protected $statusHistoryDataType = 'array';
  protected $trinoJobType = TrinoJob::class;
  protected $trinoJobDataType = '';
  protected $yarnApplicationsType = YarnApplication::class;
  protected $yarnApplicationsDataType = 'array';

  /**
   * Output only. Indicates whether the job is completed. If the value is false,
   * the job is still in progress. If true, the job is completed, and
   * status.state field will indicate if it was successful, failed, or
   * cancelled.
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
  }
  /**
   * Output only. If present, the location of miscellaneous control files which
   * can be used as part of job setup and handling. If not present, control
   * files might be placed in the same location as driver_output_uri.
   *
   * @param string $driverControlFilesUri
   */
  public function setDriverControlFilesUri($driverControlFilesUri)
  {
    $this->driverControlFilesUri = $driverControlFilesUri;
  }
  /**
   * @return string
   */
  public function getDriverControlFilesUri()
  {
    return $this->driverControlFilesUri;
  }
  /**
   * Output only. A URI pointing to the location of the stdout of the job's
   * driver program.
   *
   * @param string $driverOutputResourceUri
   */
  public function setDriverOutputResourceUri($driverOutputResourceUri)
  {
    $this->driverOutputResourceUri = $driverOutputResourceUri;
  }
  /**
   * @return string
   */
  public function getDriverOutputResourceUri()
  {
    return $this->driverOutputResourceUri;
  }
  /**
   * Optional. Driver scheduling configuration.
   *
   * @param DriverSchedulingConfig $driverSchedulingConfig
   */
  public function setDriverSchedulingConfig(DriverSchedulingConfig $driverSchedulingConfig)
  {
    $this->driverSchedulingConfig = $driverSchedulingConfig;
  }
  /**
   * @return DriverSchedulingConfig
   */
  public function getDriverSchedulingConfig()
  {
    return $this->driverSchedulingConfig;
  }
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
   * Output only. A UUID that uniquely identifies a job within the project over
   * time. This is in contrast to a user-settable reference.job_id that might be
   * reused over time.
   *
   * @param string $jobUuid
   */
  public function setJobUuid($jobUuid)
  {
    $this->jobUuid = $jobUuid;
  }
  /**
   * @return string
   */
  public function getJobUuid()
  {
    return $this->jobUuid;
  }
  /**
   * Optional. The labels to associate with this job. Label keys must contain 1
   * to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values can be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a job.
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
   * Required. Job information, including how, when, and where to run the job.
   *
   * @param JobPlacement $placement
   */
  public function setPlacement(JobPlacement $placement)
  {
    $this->placement = $placement;
  }
  /**
   * @return JobPlacement
   */
  public function getPlacement()
  {
    return $this->placement;
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
   * Optional. The fully qualified reference to the job, which can be used to
   * obtain the equivalent REST path of the job resource. If this property is
   * not specified when a job is created, the server generates a job_id.
   *
   * @param JobReference $reference
   */
  public function setReference(JobReference $reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return JobReference
   */
  public function getReference()
  {
    return $this->reference;
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
   * Output only. The job status. Additional application-specific status
   * information might be contained in the type_job and yarn_applications
   * fields.
   *
   * @param JobStatus $status
   */
  public function setStatus(JobStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return JobStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The previous job status.
   *
   * @param JobStatus[] $statusHistory
   */
  public function setStatusHistory($statusHistory)
  {
    $this->statusHistory = $statusHistory;
  }
  /**
   * @return JobStatus[]
   */
  public function getStatusHistory()
  {
    return $this->statusHistory;
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
  /**
   * Output only. The collection of YARN applications spun up by this job.Beta
   * Feature: This report is available for testing purposes only. It might be
   * changed before final release.
   *
   * @param YarnApplication[] $yarnApplications
   */
  public function setYarnApplications($yarnApplications)
  {
    $this->yarnApplications = $yarnApplications;
  }
  /**
   * @return YarnApplication[]
   */
  public function getYarnApplications()
  {
    return $this->yarnApplications;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_Dataproc_Job');
