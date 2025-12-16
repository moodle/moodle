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

class TransferSpec extends \Google\Model
{
  protected $awsS3CompatibleDataSourceType = AwsS3CompatibleData::class;
  protected $awsS3CompatibleDataSourceDataType = '';
  protected $awsS3DataSourceType = AwsS3Data::class;
  protected $awsS3DataSourceDataType = '';
  protected $azureBlobStorageDataSourceType = AzureBlobStorageData::class;
  protected $azureBlobStorageDataSourceDataType = '';
  protected $gcsDataSinkType = GcsData::class;
  protected $gcsDataSinkDataType = '';
  protected $gcsDataSourceType = GcsData::class;
  protected $gcsDataSourceDataType = '';
  protected $gcsIntermediateDataLocationType = GcsData::class;
  protected $gcsIntermediateDataLocationDataType = '';
  protected $hdfsDataSourceType = HdfsData::class;
  protected $hdfsDataSourceDataType = '';
  protected $httpDataSourceType = HttpData::class;
  protected $httpDataSourceDataType = '';
  protected $objectConditionsType = ObjectConditions::class;
  protected $objectConditionsDataType = '';
  protected $posixDataSinkType = PosixFilesystem::class;
  protected $posixDataSinkDataType = '';
  protected $posixDataSourceType = PosixFilesystem::class;
  protected $posixDataSourceDataType = '';
  /**
   * Specifies the agent pool name associated with the posix data sink. When
   * unspecified, the default name is used.
   *
   * @var string
   */
  public $sinkAgentPoolName;
  /**
   * Specifies the agent pool name associated with the posix data source. When
   * unspecified, the default name is used.
   *
   * @var string
   */
  public $sourceAgentPoolName;
  protected $transferManifestType = TransferManifest::class;
  protected $transferManifestDataType = '';
  protected $transferOptionsType = TransferOptions::class;
  protected $transferOptionsDataType = '';

  /**
   * Optional. An AWS S3 compatible data source.
   *
   * @param AwsS3CompatibleData $awsS3CompatibleDataSource
   */
  public function setAwsS3CompatibleDataSource(AwsS3CompatibleData $awsS3CompatibleDataSource)
  {
    $this->awsS3CompatibleDataSource = $awsS3CompatibleDataSource;
  }
  /**
   * @return AwsS3CompatibleData
   */
  public function getAwsS3CompatibleDataSource()
  {
    return $this->awsS3CompatibleDataSource;
  }
  /**
   * Optional. An AWS S3 data source.
   *
   * @param AwsS3Data $awsS3DataSource
   */
  public function setAwsS3DataSource(AwsS3Data $awsS3DataSource)
  {
    $this->awsS3DataSource = $awsS3DataSource;
  }
  /**
   * @return AwsS3Data
   */
  public function getAwsS3DataSource()
  {
    return $this->awsS3DataSource;
  }
  /**
   * Optional. An Azure Blob Storage data source.
   *
   * @param AzureBlobStorageData $azureBlobStorageDataSource
   */
  public function setAzureBlobStorageDataSource(AzureBlobStorageData $azureBlobStorageDataSource)
  {
    $this->azureBlobStorageDataSource = $azureBlobStorageDataSource;
  }
  /**
   * @return AzureBlobStorageData
   */
  public function getAzureBlobStorageDataSource()
  {
    return $this->azureBlobStorageDataSource;
  }
  /**
   * Optional. A Cloud Storage data sink.
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
   * Optional. A Cloud Storage data source.
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
   * For transfers between file systems, specifies a Cloud Storage bucket to be
   * used as an intermediate location through which to transfer data. See
   * [Transfer data between file systems](https://cloud.google.com/storage-
   * transfer/docs/file-to-file) for more information.
   *
   * @param GcsData $gcsIntermediateDataLocation
   */
  public function setGcsIntermediateDataLocation(GcsData $gcsIntermediateDataLocation)
  {
    $this->gcsIntermediateDataLocation = $gcsIntermediateDataLocation;
  }
  /**
   * @return GcsData
   */
  public function getGcsIntermediateDataLocation()
  {
    return $this->gcsIntermediateDataLocation;
  }
  /**
   * Optional. An HDFS cluster data source.
   *
   * @param HdfsData $hdfsDataSource
   */
  public function setHdfsDataSource(HdfsData $hdfsDataSource)
  {
    $this->hdfsDataSource = $hdfsDataSource;
  }
  /**
   * @return HdfsData
   */
  public function getHdfsDataSource()
  {
    return $this->hdfsDataSource;
  }
  /**
   * Optional. An HTTP URL data source.
   *
   * @param HttpData $httpDataSource
   */
  public function setHttpDataSource(HttpData $httpDataSource)
  {
    $this->httpDataSource = $httpDataSource;
  }
  /**
   * @return HttpData
   */
  public function getHttpDataSource()
  {
    return $this->httpDataSource;
  }
  /**
   * Only objects that satisfy these object conditions are included in the set
   * of data source and data sink objects. Object conditions based on objects'
   * "last modification time" do not exclude objects in a data sink.
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
   * Optional. A POSIX Filesystem data sink.
   *
   * @param PosixFilesystem $posixDataSink
   */
  public function setPosixDataSink(PosixFilesystem $posixDataSink)
  {
    $this->posixDataSink = $posixDataSink;
  }
  /**
   * @return PosixFilesystem
   */
  public function getPosixDataSink()
  {
    return $this->posixDataSink;
  }
  /**
   * Optional. A POSIX Filesystem data source.
   *
   * @param PosixFilesystem $posixDataSource
   */
  public function setPosixDataSource(PosixFilesystem $posixDataSource)
  {
    $this->posixDataSource = $posixDataSource;
  }
  /**
   * @return PosixFilesystem
   */
  public function getPosixDataSource()
  {
    return $this->posixDataSource;
  }
  /**
   * Specifies the agent pool name associated with the posix data sink. When
   * unspecified, the default name is used.
   *
   * @param string $sinkAgentPoolName
   */
  public function setSinkAgentPoolName($sinkAgentPoolName)
  {
    $this->sinkAgentPoolName = $sinkAgentPoolName;
  }
  /**
   * @return string
   */
  public function getSinkAgentPoolName()
  {
    return $this->sinkAgentPoolName;
  }
  /**
   * Specifies the agent pool name associated with the posix data source. When
   * unspecified, the default name is used.
   *
   * @param string $sourceAgentPoolName
   */
  public function setSourceAgentPoolName($sourceAgentPoolName)
  {
    $this->sourceAgentPoolName = $sourceAgentPoolName;
  }
  /**
   * @return string
   */
  public function getSourceAgentPoolName()
  {
    return $this->sourceAgentPoolName;
  }
  /**
   * A manifest file provides a list of objects to be transferred from the data
   * source. This field points to the location of the manifest file. Otherwise,
   * the entire source bucket is used. ObjectConditions still apply.
   *
   * @param TransferManifest $transferManifest
   */
  public function setTransferManifest(TransferManifest $transferManifest)
  {
    $this->transferManifest = $transferManifest;
  }
  /**
   * @return TransferManifest
   */
  public function getTransferManifest()
  {
    return $this->transferManifest;
  }
  /**
   * If the option delete_objects_unique_in_sink is `true` and time-based object
   * conditions such as 'last modification time' are specified, the request
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
class_alias(TransferSpec::class, 'Google_Service_Storagetransfer_TransferSpec');
