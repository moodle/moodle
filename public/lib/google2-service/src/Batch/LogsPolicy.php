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

namespace Google\Service\Batch;

class LogsPolicy extends \Google\Model
{
  /**
   * (Default) Logs are not preserved.
   */
  public const DESTINATION_DESTINATION_UNSPECIFIED = 'DESTINATION_UNSPECIFIED';
  /**
   * Logs are streamed to Cloud Logging. Optionally, you can configure
   * additional settings in the `cloudLoggingOption` field.
   */
  public const DESTINATION_CLOUD_LOGGING = 'CLOUD_LOGGING';
  /**
   * Logs are saved to the file path specified in the `logsPath` field.
   */
  public const DESTINATION_PATH = 'PATH';
  protected $cloudLoggingOptionType = CloudLoggingOption::class;
  protected $cloudLoggingOptionDataType = '';
  /**
   * If and where logs should be saved.
   *
   * @var string
   */
  public $destination;
  /**
   * When `destination` is set to `PATH`, you must set this field to the path
   * where you want logs to be saved. This path can point to a local directory
   * on the VM or (if congifured) a directory under the mount path of any Cloud
   * Storage bucket, network file system (NFS), or writable persistent disk that
   * is mounted to the job. For example, if the job has a bucket with
   * `mountPath` set to `/mnt/disks/my-bucket`, you can write logs to the root
   * directory of the `remotePath` of that bucket by setting this field to
   * `/mnt/disks/my-bucket/`.
   *
   * @var string
   */
  public $logsPath;

  /**
   * Optional. When `destination` is set to `CLOUD_LOGGING`, you can optionally
   * set this field to configure additional settings for Cloud Logging.
   *
   * @param CloudLoggingOption $cloudLoggingOption
   */
  public function setCloudLoggingOption(CloudLoggingOption $cloudLoggingOption)
  {
    $this->cloudLoggingOption = $cloudLoggingOption;
  }
  /**
   * @return CloudLoggingOption
   */
  public function getCloudLoggingOption()
  {
    return $this->cloudLoggingOption;
  }
  /**
   * If and where logs should be saved.
   *
   * Accepted values: DESTINATION_UNSPECIFIED, CLOUD_LOGGING, PATH
   *
   * @param self::DESTINATION_* $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return self::DESTINATION_*
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * When `destination` is set to `PATH`, you must set this field to the path
   * where you want logs to be saved. This path can point to a local directory
   * on the VM or (if congifured) a directory under the mount path of any Cloud
   * Storage bucket, network file system (NFS), or writable persistent disk that
   * is mounted to the job. For example, if the job has a bucket with
   * `mountPath` set to `/mnt/disks/my-bucket`, you can write logs to the root
   * directory of the `remotePath` of that bucket by setting this field to
   * `/mnt/disks/my-bucket/`.
   *
   * @param string $logsPath
   */
  public function setLogsPath($logsPath)
  {
    $this->logsPath = $logsPath;
  }
  /**
   * @return string
   */
  public function getLogsPath()
  {
    return $this->logsPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogsPolicy::class, 'Google_Service_Batch_LogsPolicy');
