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

namespace Google\Service\Storagetransfer;

class ReplicationSpec extends \Google\Model
{
  protected $gcsDataSinkType = GcsData::class;
  protected $gcsDataSinkDataType = '';
  protected $gcsDataSourceType = GcsData::class;
  protected $gcsDataSourceDataType = '';
  protected $objectConditionsType = ObjectConditions::class;
  protected $objectConditionsDataType = '';
  protected $transferOptionsType = TransferOptions::class;
  protected $transferOptionsDataType = '';

  /**
   * The Cloud Storage bucket to which to replicate objects.
   *
   * @param GcsData $gcsDataSink
   */
  public function setGcsDataSink(GcsData $gcsDataSink)
  {
    $this->gcsDataSink = $gcsDataSink;
  }
  /**
   * @return GcsData
   */
  public function getGcsDataSink()
  {
    return $this->gcsDataSink;
  }
  /**
   * The Cloud Storage bucket from which to replicate objects.
   *
   * @param GcsData $gcsDataSource
   */
  public function setGcsDataSource(GcsData $gcsDataSource)
  {
    $this->gcsDataSource = $gcsDataSource;
  }
  /**
   * @return GcsData
   */
  public function getGcsDataSource()
  {
    return $this->gcsDataSource;
  }
  /**
   * Object conditions that determine which objects are transferred. For
   * replication jobs, only `include_prefixes` and `exclude_prefixes` are
   * supported.
   *
   * @param ObjectConditions $objectConditions
   */
  public function setObjectConditions(ObjectConditions $objectConditions)
  {
    $this->objectConditions = $objectConditions;
  }
  /**
   * @return ObjectConditions
   */
  public function getObjectConditions()
  {
    return $this->objectConditions;
  }
  /**
   * Specifies the metadata options to be applied during replication. Delete
   * options are not supported. If a delete option is specified, the request
   * fails with an INVALID_ARGUMENT error.
   *
   * @param TransferOptions $transferOptions
   */
  public function setTransferOptions(TransferOptions $transferOptions)
  {
    $this->transferOptions = $transferOptions;
  }
  /**
   * @return TransferOptions
   */
  public function getTransferOptions()
  {
    return $this->transferOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicationSpec::class, 'Google_Service_Storagetransfer_ReplicationSpec');
