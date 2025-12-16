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

class EncryptionConfig extends \Google\Model
{
  /**
   * Optional. The Cloud KMS key resource name to use for persistent disk
   * encryption for all instances in the cluster. See Use CMEK with cluster data
   * (https://cloud.google.com//dataproc/docs/concepts/configuring-
   * clusters/customer-managed-encryption#use_cmek_with_cluster_data) for more
   * information.
   *
   * @var string
   */
  public $gcePdKmsKeyName;
  /**
   * Optional. The Cloud KMS key resource name to use for cluster persistent
   * disk and job argument encryption. See Use CMEK with cluster data
   * (https://cloud.google.com//dataproc/docs/concepts/configuring-
   * clusters/customer-managed-encryption#use_cmek_with_cluster_data) for more
   * information.When this key resource name is provided, the following job
   * arguments of the following job types submitted to the cluster are encrypted
   * using CMEK: FlinkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/FlinkJob)
   * HadoopJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/HadoopJob)
   * SparkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkJob)
   * SparkRJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkRJob)
   * PySparkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PySparkJob)
   * SparkSqlJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkSqlJob)
   * scriptVariables and queryList.queries HiveJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/HiveJob)
   * scriptVariables and queryList.queries PigJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PigJob)
   * scriptVariables and queryList.queries PrestoJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PrestoJob)
   * scriptVariables and queryList.queries
   *
   * @var string
   */
  public $kmsKey;

  /**
   * Optional. The Cloud KMS key resource name to use for persistent disk
   * encryption for all instances in the cluster. See Use CMEK with cluster data
   * (https://cloud.google.com//dataproc/docs/concepts/configuring-
   * clusters/customer-managed-encryption#use_cmek_with_cluster_data) for more
   * information.
   *
   * @param string $gcePdKmsKeyName
   */
  public function setGcePdKmsKeyName($gcePdKmsKeyName)
  {
    $this->gcePdKmsKeyName = $gcePdKmsKeyName;
  }
  /**
   * @return string
   */
  public function getGcePdKmsKeyName()
  {
    return $this->gcePdKmsKeyName;
  }
  /**
   * Optional. The Cloud KMS key resource name to use for cluster persistent
   * disk and job argument encryption. See Use CMEK with cluster data
   * (https://cloud.google.com//dataproc/docs/concepts/configuring-
   * clusters/customer-managed-encryption#use_cmek_with_cluster_data) for more
   * information.When this key resource name is provided, the following job
   * arguments of the following job types submitted to the cluster are encrypted
   * using CMEK: FlinkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/FlinkJob)
   * HadoopJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/HadoopJob)
   * SparkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkJob)
   * SparkRJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkRJob)
   * PySparkJob args
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PySparkJob)
   * SparkSqlJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/SparkSqlJob)
   * scriptVariables and queryList.queries HiveJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/HiveJob)
   * scriptVariables and queryList.queries PigJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PigJob)
   * scriptVariables and queryList.queries PrestoJob
   * (https://cloud.google.com/dataproc/docs/reference/rest/v1/PrestoJob)
   * scriptVariables and queryList.queries
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionConfig::class, 'Google_Service_Dataproc_EncryptionConfig');
