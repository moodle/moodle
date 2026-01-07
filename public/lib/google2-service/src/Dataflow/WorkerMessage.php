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

namespace Google\Service\Dataflow;

class WorkerMessage extends \Google\Model
{
  protected $dataSamplingReportType = DataSamplingReport::class;
  protected $dataSamplingReportDataType = '';
  /**
   * Labels are used to group WorkerMessages. For example, a worker_message
   * about a particular container might have the labels: { "JOB_ID":
   * "2015-04-22", "WORKER_ID": "wordcount-vm-2015…" "CONTAINER_TYPE": "worker",
   * "CONTAINER_ID": "ac1234def"} Label tags typically correspond to Label enum
   * values. However, for ease of development other strings can be used as tags.
   * LABEL_UNSPECIFIED should not be used here.
   *
   * @var string[]
   */
  public $labels;
  protected $perWorkerMetricsType = PerWorkerMetrics::class;
  protected $perWorkerMetricsDataType = '';
  protected $streamingScalingReportType = StreamingScalingReport::class;
  protected $streamingScalingReportDataType = '';
  /**
   * The timestamp of the worker_message.
   *
   * @var string
   */
  public $time;
  protected $workerHealthReportType = WorkerHealthReport::class;
  protected $workerHealthReportDataType = '';
  protected $workerLifecycleEventType = WorkerLifecycleEvent::class;
  protected $workerLifecycleEventDataType = '';
  protected $workerMessageCodeType = WorkerMessageCode::class;
  protected $workerMessageCodeDataType = '';
  protected $workerMetricsType = ResourceUtilizationReport::class;
  protected $workerMetricsDataType = '';
  protected $workerShutdownNoticeType = WorkerShutdownNotice::class;
  protected $workerShutdownNoticeDataType = '';
  protected $workerThreadScalingReportType = WorkerThreadScalingReport::class;
  protected $workerThreadScalingReportDataType = '';

  /**
   * Optional. Contains metrics related to go/dataflow-data-sampling-telemetry.
   *
   * @param DataSamplingReport $dataSamplingReport
   */
  public function setDataSamplingReport(DataSamplingReport $dataSamplingReport)
  {
    $this->dataSamplingReport = $dataSamplingReport;
  }
  /**
   * @return DataSamplingReport
   */
  public function getDataSamplingReport()
  {
    return $this->dataSamplingReport;
  }
  /**
   * Labels are used to group WorkerMessages. For example, a worker_message
   * about a particular container might have the labels: { "JOB_ID":
   * "2015-04-22", "WORKER_ID": "wordcount-vm-2015…" "CONTAINER_TYPE": "worker",
   * "CONTAINER_ID": "ac1234def"} Label tags typically correspond to Label enum
   * values. However, for ease of development other strings can be used as tags.
   * LABEL_UNSPECIFIED should not be used here.
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
   * System defined metrics for this worker.
   *
   * @param PerWorkerMetrics $perWorkerMetrics
   */
  public function setPerWorkerMetrics(PerWorkerMetrics $perWorkerMetrics)
  {
    $this->perWorkerMetrics = $perWorkerMetrics;
  }
  /**
   * @return PerWorkerMetrics
   */
  public function getPerWorkerMetrics()
  {
    return $this->perWorkerMetrics;
  }
  /**
   * Contains per-user worker telemetry used in streaming autoscaling.
   *
   * @param StreamingScalingReport $streamingScalingReport
   */
  public function setStreamingScalingReport(StreamingScalingReport $streamingScalingReport)
  {
    $this->streamingScalingReport = $streamingScalingReport;
  }
  /**
   * @return StreamingScalingReport
   */
  public function getStreamingScalingReport()
  {
    return $this->streamingScalingReport;
  }
  /**
   * The timestamp of the worker_message.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
  /**
   * The health of a worker.
   *
   * @param WorkerHealthReport $workerHealthReport
   */
  public function setWorkerHealthReport(WorkerHealthReport $workerHealthReport)
  {
    $this->workerHealthReport = $workerHealthReport;
  }
  /**
   * @return WorkerHealthReport
   */
  public function getWorkerHealthReport()
  {
    return $this->workerHealthReport;
  }
  /**
   * Record of worker lifecycle events.
   *
   * @param WorkerLifecycleEvent $workerLifecycleEvent
   */
  public function setWorkerLifecycleEvent(WorkerLifecycleEvent $workerLifecycleEvent)
  {
    $this->workerLifecycleEvent = $workerLifecycleEvent;
  }
  /**
   * @return WorkerLifecycleEvent
   */
  public function getWorkerLifecycleEvent()
  {
    return $this->workerLifecycleEvent;
  }
  /**
   * A worker message code.
   *
   * @param WorkerMessageCode $workerMessageCode
   */
  public function setWorkerMessageCode(WorkerMessageCode $workerMessageCode)
  {
    $this->workerMessageCode = $workerMessageCode;
  }
  /**
   * @return WorkerMessageCode
   */
  public function getWorkerMessageCode()
  {
    return $this->workerMessageCode;
  }
  /**
   * Resource metrics reported by workers.
   *
   * @param ResourceUtilizationReport $workerMetrics
   */
  public function setWorkerMetrics(ResourceUtilizationReport $workerMetrics)
  {
    $this->workerMetrics = $workerMetrics;
  }
  /**
   * @return ResourceUtilizationReport
   */
  public function getWorkerMetrics()
  {
    return $this->workerMetrics;
  }
  /**
   * Shutdown notice by workers.
   *
   * @param WorkerShutdownNotice $workerShutdownNotice
   */
  public function setWorkerShutdownNotice(WorkerShutdownNotice $workerShutdownNotice)
  {
    $this->workerShutdownNotice = $workerShutdownNotice;
  }
  /**
   * @return WorkerShutdownNotice
   */
  public function getWorkerShutdownNotice()
  {
    return $this->workerShutdownNotice;
  }
  /**
   * Thread scaling information reported by workers.
   *
   * @param WorkerThreadScalingReport $workerThreadScalingReport
   */
  public function setWorkerThreadScalingReport(WorkerThreadScalingReport $workerThreadScalingReport)
  {
    $this->workerThreadScalingReport = $workerThreadScalingReport;
  }
  /**
   * @return WorkerThreadScalingReport
   */
  public function getWorkerThreadScalingReport()
  {
    return $this->workerThreadScalingReport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerMessage::class, 'Google_Service_Dataflow_WorkerMessage');
