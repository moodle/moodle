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

namespace Google\Service\Compute;

class UsageExportLocation extends \Google\Model
{
  /**
   * The name of an existing bucket in Cloud Storage where the usage report
   * object is stored. The Google Service Account is granted write access to
   * this bucket. This can either be the bucket name by itself, such asexample-
   * bucket, or the bucket name with gs:// or https://storage.googleapis.com/ in
   * front of it, such as gs://example-bucket.
   *
   * @var string
   */
  public $bucketName;
  /**
   * An optional prefix for the name of the usage report object stored
   * inbucketName. If not supplied, defaults tousage_gce. The report is stored
   * as a CSV file namedreport_name_prefix_gce_YYYYMMDD.csv whereYYYYMMDD is the
   * day of the usage according to Pacific Time. If you supply a prefix, it
   * should conform to Cloud Storageobject naming conventions.
   *
   * @var string
   */
  public $reportNamePrefix;

  /**
   * The name of an existing bucket in Cloud Storage where the usage report
   * object is stored. The Google Service Account is granted write access to
   * this bucket. This can either be the bucket name by itself, such asexample-
   * bucket, or the bucket name with gs:// or https://storage.googleapis.com/ in
   * front of it, such as gs://example-bucket.
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * An optional prefix for the name of the usage report object stored
   * inbucketName. If not supplied, defaults tousage_gce. The report is stored
   * as a CSV file namedreport_name_prefix_gce_YYYYMMDD.csv whereYYYYMMDD is the
   * day of the usage according to Pacific Time. If you supply a prefix, it
   * should conform to Cloud Storageobject naming conventions.
   *
   * @param string $reportNamePrefix
   */
  public function setReportNamePrefix($reportNamePrefix)
  {
    $this->reportNamePrefix = $reportNamePrefix;
  }
  /**
   * @return string
   */
  public function getReportNamePrefix()
  {
    return $this->reportNamePrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageExportLocation::class, 'Google_Service_Compute_UsageExportLocation');
