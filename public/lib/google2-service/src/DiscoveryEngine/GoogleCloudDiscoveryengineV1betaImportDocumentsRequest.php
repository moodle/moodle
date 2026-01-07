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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaImportDocumentsRequest extends \Google\Model
{
  /**
   * @var bool
   */
  public $autoGenerateIds;
  protected $bigquerySourceType = GoogleCloudDiscoveryengineV1betaBigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $bigtableSourceType = GoogleCloudDiscoveryengineV1betaBigtableSource::class;
  protected $bigtableSourceDataType = '';
  protected $cloudSqlSourceType = GoogleCloudDiscoveryengineV1betaCloudSqlSource::class;
  protected $cloudSqlSourceDataType = '';
  protected $errorConfigType = GoogleCloudDiscoveryengineV1betaImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $fhirStoreSourceType = GoogleCloudDiscoveryengineV1betaFhirStoreSource::class;
  protected $fhirStoreSourceDataType = '';
  protected $firestoreSourceType = GoogleCloudDiscoveryengineV1betaFirestoreSource::class;
  protected $firestoreSourceDataType = '';
  protected $gcsSourceType = GoogleCloudDiscoveryengineV1betaGcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * @var string
   */
  public $idField;
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1betaImportDocumentsRequestInlineSource::class;
  protected $inlineSourceDataType = '';
  /**
   * @var string
   */
  public $reconciliationMode;
  protected $spannerSourceType = GoogleCloudDiscoveryengineV1betaSpannerSource::class;
  protected $spannerSourceDataType = '';
  /**
   * @var string
   */
  public $updateMask;

  /**
   * @param bool
   */
  public function setAutoGenerateIds($autoGenerateIds)
  {
    $this->autoGenerateIds = $autoGenerateIds;
  }
  /**
   * @return bool
   */
  public function getAutoGenerateIds()
  {
    return $this->autoGenerateIds;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaBigQuerySource
   */
  public function setBigquerySource(GoogleCloudDiscoveryengineV1betaBigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaBigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaBigtableSource
   */
  public function setBigtableSource(GoogleCloudDiscoveryengineV1betaBigtableSource $bigtableSource)
  {
    $this->bigtableSource = $bigtableSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaBigtableSource
   */
  public function getBigtableSource()
  {
    return $this->bigtableSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaCloudSqlSource
   */
  public function setCloudSqlSource(GoogleCloudDiscoveryengineV1betaCloudSqlSource $cloudSqlSource)
  {
    $this->cloudSqlSource = $cloudSqlSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCloudSqlSource
   */
  public function getCloudSqlSource()
  {
    return $this->cloudSqlSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1betaImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaFhirStoreSource
   */
  public function setFhirStoreSource(GoogleCloudDiscoveryengineV1betaFhirStoreSource $fhirStoreSource)
  {
    $this->fhirStoreSource = $fhirStoreSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaFhirStoreSource
   */
  public function getFhirStoreSource()
  {
    return $this->fhirStoreSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaFirestoreSource
   */
  public function setFirestoreSource(GoogleCloudDiscoveryengineV1betaFirestoreSource $firestoreSource)
  {
    $this->firestoreSource = $firestoreSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaFirestoreSource
   */
  public function getFirestoreSource()
  {
    return $this->firestoreSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaGcsSource
   */
  public function setGcsSource(GoogleCloudDiscoveryengineV1betaGcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaGcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * @param string
   */
  public function setIdField($idField)
  {
    $this->idField = $idField;
  }
  /**
   * @return string
   */
  public function getIdField()
  {
    return $this->idField;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaImportDocumentsRequestInlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1betaImportDocumentsRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaImportDocumentsRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
  /**
   * @param string
   */
  public function setReconciliationMode($reconciliationMode)
  {
    $this->reconciliationMode = $reconciliationMode;
  }
  /**
   * @return string
   */
  public function getReconciliationMode()
  {
    return $this->reconciliationMode;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaSpannerSource
   */
  public function setSpannerSource(GoogleCloudDiscoveryengineV1betaSpannerSource $spannerSource)
  {
    $this->spannerSource = $spannerSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSpannerSource
   */
  public function getSpannerSource()
  {
    return $this->spannerSource;
  }
  /**
   * @param string
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaImportDocumentsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaImportDocumentsRequest');
