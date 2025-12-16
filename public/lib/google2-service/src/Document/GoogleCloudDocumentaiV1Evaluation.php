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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1Evaluation extends \Google\Model
{
  protected $allEntitiesMetricsType = GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics::class;
  protected $allEntitiesMetricsDataType = '';
  /**
   * The time that the evaluation was created.
   *
   * @var string
   */
  public $createTime;
  protected $documentCountersType = GoogleCloudDocumentaiV1EvaluationCounters::class;
  protected $documentCountersDataType = '';
  protected $entityMetricsType = GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics::class;
  protected $entityMetricsDataType = 'map';
  /**
   * The KMS key name used for encryption.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * The KMS key version with which data is encrypted.
   *
   * @var string
   */
  public $kmsKeyVersionName;
  /**
   * The resource name of the evaluation. Format: `projects/{project}/locations/
   * {location}/processors/{processor}/processorVersions/{processor_version}/eva
   * luations/{evaluation}`
   *
   * @var string
   */
  public $name;

  /**
   * Metrics for all the entities in aggregate.
   *
   * @param GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics $allEntitiesMetrics
   */
  public function setAllEntitiesMetrics(GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics $allEntitiesMetrics)
  {
    $this->allEntitiesMetrics = $allEntitiesMetrics;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics
   */
  public function getAllEntitiesMetrics()
  {
    return $this->allEntitiesMetrics;
  }
  /**
   * The time that the evaluation was created.
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
   * Counters for the documents used in the evaluation.
   *
   * @param GoogleCloudDocumentaiV1EvaluationCounters $documentCounters
   */
  public function setDocumentCounters(GoogleCloudDocumentaiV1EvaluationCounters $documentCounters)
  {
    $this->documentCounters = $documentCounters;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationCounters
   */
  public function getDocumentCounters()
  {
    return $this->documentCounters;
  }
  /**
   * Metrics across confidence levels, for different entities.
   *
   * @param GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics[] $entityMetrics
   */
  public function setEntityMetrics($entityMetrics)
  {
    $this->entityMetrics = $entityMetrics;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics[]
   */
  public function getEntityMetrics()
  {
    return $this->entityMetrics;
  }
  /**
   * The KMS key name used for encryption.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * The KMS key version with which data is encrypted.
   *
   * @param string $kmsKeyVersionName
   */
  public function setKmsKeyVersionName($kmsKeyVersionName)
  {
    $this->kmsKeyVersionName = $kmsKeyVersionName;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersionName()
  {
    return $this->kmsKeyVersionName;
  }
  /**
   * The resource name of the evaluation. Format: `projects/{project}/locations/
   * {location}/processors/{processor}/processorVersions/{processor_version}/eva
   * luations/{evaluation}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1Evaluation::class, 'Google_Service_Document_GoogleCloudDocumentaiV1Evaluation');
