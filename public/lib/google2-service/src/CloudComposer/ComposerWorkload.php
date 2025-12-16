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

namespace Google\Service\CloudComposer;

class ComposerWorkload extends \Google\Model
{
  /**
   * Not able to determine the type of the workload.
   */
  public const TYPE_COMPOSER_WORKLOAD_TYPE_UNSPECIFIED = 'COMPOSER_WORKLOAD_TYPE_UNSPECIFIED';
  /**
   * Celery worker.
   */
  public const TYPE_CELERY_WORKER = 'CELERY_WORKER';
  /**
   * Kubernetes worker.
   */
  public const TYPE_KUBERNETES_WORKER = 'KUBERNETES_WORKER';
  /**
   * Workload created by Kubernetes Pod Operator.
   */
  public const TYPE_KUBERNETES_OPERATOR_POD = 'KUBERNETES_OPERATOR_POD';
  /**
   * Airflow scheduler.
   */
  public const TYPE_SCHEDULER = 'SCHEDULER';
  /**
   * Airflow Dag processor.
   */
  public const TYPE_DAG_PROCESSOR = 'DAG_PROCESSOR';
  /**
   * Airflow triggerer.
   */
  public const TYPE_TRIGGERER = 'TRIGGERER';
  /**
   * Airflow web server UI.
   */
  public const TYPE_WEB_SERVER = 'WEB_SERVER';
  /**
   * Redis.
   */
  public const TYPE_REDIS = 'REDIS';
  /**
   * Name of a workload.
   *
   * @var string
   */
  public $name;
  protected $statusType = ComposerWorkloadStatus::class;
  protected $statusDataType = '';
  /**
   * Type of a workload.
   *
   * @var string
   */
  public $type;

  /**
   * Name of a workload.
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
  /**
   * Output only. Status of a workload.
   *
   * @param ComposerWorkloadStatus $status
   */
  public function setStatus(ComposerWorkloadStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ComposerWorkloadStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Type of a workload.
   *
   * Accepted values: COMPOSER_WORKLOAD_TYPE_UNSPECIFIED, CELERY_WORKER,
   * KUBERNETES_WORKER, KUBERNETES_OPERATOR_POD, SCHEDULER, DAG_PROCESSOR,
   * TRIGGERER, WEB_SERVER, REDIS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComposerWorkload::class, 'Google_Service_CloudComposer_ComposerWorkload');
