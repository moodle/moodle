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

class DiagnoseClusterRequest extends \Google\Collection
{
  /**
   * Tarball Access unspecified. Falls back to default access of the bucket
   */
  public const TARBALL_ACCESS_TARBALL_ACCESS_UNSPECIFIED = 'TARBALL_ACCESS_UNSPECIFIED';
  /**
   * Google Cloud Support group has read access to the diagnostic tarball
   */
  public const TARBALL_ACCESS_GOOGLE_CLOUD_SUPPORT = 'GOOGLE_CLOUD_SUPPORT';
  /**
   * Google Cloud Dataproc Diagnose service account has read access to the
   * diagnostic tarball
   */
  public const TARBALL_ACCESS_GOOGLE_DATAPROC_DIAGNOSE = 'GOOGLE_DATAPROC_DIAGNOSE';
  protected $collection_key = 'yarnApplicationIds';
  protected $diagnosisIntervalType = Interval::class;
  protected $diagnosisIntervalDataType = '';
  /**
   * Optional. DEPRECATED Specifies the job on which diagnosis is to be
   * performed. Format: projects/{project}/regions/{region}/jobs/{job}
   *
   * @deprecated
   * @var string
   */
  public $job;
  /**
   * Optional. Specifies a list of jobs on which diagnosis is to be performed.
   * Format: projects/{project}/regions/{region}/jobs/{job}
   *
   * @var string[]
   */
  public $jobs;
  /**
   * Optional. (Optional) The access type to the diagnostic tarball. If not
   * specified, falls back to default access of the bucket
   *
   * @var string
   */
  public $tarballAccess;
  /**
   * Optional. (Optional) The output Cloud Storage directory for the diagnostic
   * tarball. If not specified, a task-specific directory in the cluster's
   * staging bucket will be used.
   *
   * @var string
   */
  public $tarballGcsDir;
  /**
   * Optional. DEPRECATED Specifies the yarn application on which diagnosis is
   * to be performed.
   *
   * @deprecated
   * @var string
   */
  public $yarnApplicationId;
  /**
   * Optional. Specifies a list of yarn applications on which diagnosis is to be
   * performed.
   *
   * @var string[]
   */
  public $yarnApplicationIds;

  /**
   * Optional. Time interval in which diagnosis should be carried out on the
   * cluster.
   *
   * @param Interval $diagnosisInterval
   */
  public function setDiagnosisInterval(Interval $diagnosisInterval)
  {
    $this->diagnosisInterval = $diagnosisInterval;
  }
  /**
   * @return Interval
   */
  public function getDiagnosisInterval()
  {
    return $this->diagnosisInterval;
  }
  /**
   * Optional. DEPRECATED Specifies the job on which diagnosis is to be
   * performed. Format: projects/{project}/regions/{region}/jobs/{job}
   *
   * @deprecated
   * @param string $job
   */
  public function setJob($job)
  {
    $this->job = $job;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getJob()
  {
    return $this->job;
  }
  /**
   * Optional. Specifies a list of jobs on which diagnosis is to be performed.
   * Format: projects/{project}/regions/{region}/jobs/{job}
   *
   * @param string[] $jobs
   */
  public function setJobs($jobs)
  {
    $this->jobs = $jobs;
  }
  /**
   * @return string[]
   */
  public function getJobs()
  {
    return $this->jobs;
  }
  /**
   * Optional. (Optional) The access type to the diagnostic tarball. If not
   * specified, falls back to default access of the bucket
   *
   * Accepted values: TARBALL_ACCESS_UNSPECIFIED, GOOGLE_CLOUD_SUPPORT,
   * GOOGLE_DATAPROC_DIAGNOSE
   *
   * @param self::TARBALL_ACCESS_* $tarballAccess
   */
  public function setTarballAccess($tarballAccess)
  {
    $this->tarballAccess = $tarballAccess;
  }
  /**
   * @return self::TARBALL_ACCESS_*
   */
  public function getTarballAccess()
  {
    return $this->tarballAccess;
  }
  /**
   * Optional. (Optional) The output Cloud Storage directory for the diagnostic
   * tarball. If not specified, a task-specific directory in the cluster's
   * staging bucket will be used.
   *
   * @param string $tarballGcsDir
   */
  public function setTarballGcsDir($tarballGcsDir)
  {
    $this->tarballGcsDir = $tarballGcsDir;
  }
  /**
   * @return string
   */
  public function getTarballGcsDir()
  {
    return $this->tarballGcsDir;
  }
  /**
   * Optional. DEPRECATED Specifies the yarn application on which diagnosis is
   * to be performed.
   *
   * @deprecated
   * @param string $yarnApplicationId
   */
  public function setYarnApplicationId($yarnApplicationId)
  {
    $this->yarnApplicationId = $yarnApplicationId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getYarnApplicationId()
  {
    return $this->yarnApplicationId;
  }
  /**
   * Optional. Specifies a list of yarn applications on which diagnosis is to be
   * performed.
   *
   * @param string[] $yarnApplicationIds
   */
  public function setYarnApplicationIds($yarnApplicationIds)
  {
    $this->yarnApplicationIds = $yarnApplicationIds;
  }
  /**
   * @return string[]
   */
  public function getYarnApplicationIds()
  {
    return $this->yarnApplicationIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiagnoseClusterRequest::class, 'Google_Service_Dataproc_DiagnoseClusterRequest');
