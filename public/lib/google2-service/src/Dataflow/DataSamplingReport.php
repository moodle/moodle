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

class DataSamplingReport extends \Google\Model
{
  /**
   * Optional. Delta of bytes written to file from previous report.
   *
   * @var string
   */
  public $bytesWrittenDelta;
  /**
   * Optional. Delta of bytes sampled from previous report.
   *
   * @var string
   */
  public $elementsSampledBytes;
  /**
   * Optional. Delta of number of elements sampled from previous report.
   *
   * @var string
   */
  public $elementsSampledCount;
  /**
   * Optional. Delta of number of samples taken from user code exceptions from
   * previous report.
   *
   * @var string
   */
  public $exceptionsSampledCount;
  /**
   * Optional. Delta of number of PCollections sampled from previous report.
   *
   * @var string
   */
  public $pcollectionsSampledCount;
  /**
   * Optional. Delta of errors counts from persisting the samples from previous
   * report.
   *
   * @var string
   */
  public $persistenceErrorsCount;
  /**
   * Optional. Delta of errors counts from retrieving, or translating the
   * samples from previous report.
   *
   * @var string
   */
  public $translationErrorsCount;

  /**
   * Optional. Delta of bytes written to file from previous report.
   *
   * @param string $bytesWrittenDelta
   */
  public function setBytesWrittenDelta($bytesWrittenDelta)
  {
    $this->bytesWrittenDelta = $bytesWrittenDelta;
  }
  /**
   * @return string
   */
  public function getBytesWrittenDelta()
  {
    return $this->bytesWrittenDelta;
  }
  /**
   * Optional. Delta of bytes sampled from previous report.
   *
   * @param string $elementsSampledBytes
   */
  public function setElementsSampledBytes($elementsSampledBytes)
  {
    $this->elementsSampledBytes = $elementsSampledBytes;
  }
  /**
   * @return string
   */
  public function getElementsSampledBytes()
  {
    return $this->elementsSampledBytes;
  }
  /**
   * Optional. Delta of number of elements sampled from previous report.
   *
   * @param string $elementsSampledCount
   */
  public function setElementsSampledCount($elementsSampledCount)
  {
    $this->elementsSampledCount = $elementsSampledCount;
  }
  /**
   * @return string
   */
  public function getElementsSampledCount()
  {
    return $this->elementsSampledCount;
  }
  /**
   * Optional. Delta of number of samples taken from user code exceptions from
   * previous report.
   *
   * @param string $exceptionsSampledCount
   */
  public function setExceptionsSampledCount($exceptionsSampledCount)
  {
    $this->exceptionsSampledCount = $exceptionsSampledCount;
  }
  /**
   * @return string
   */
  public function getExceptionsSampledCount()
  {
    return $this->exceptionsSampledCount;
  }
  /**
   * Optional. Delta of number of PCollections sampled from previous report.
   *
   * @param string $pcollectionsSampledCount
   */
  public function setPcollectionsSampledCount($pcollectionsSampledCount)
  {
    $this->pcollectionsSampledCount = $pcollectionsSampledCount;
  }
  /**
   * @return string
   */
  public function getPcollectionsSampledCount()
  {
    return $this->pcollectionsSampledCount;
  }
  /**
   * Optional. Delta of errors counts from persisting the samples from previous
   * report.
   *
   * @param string $persistenceErrorsCount
   */
  public function setPersistenceErrorsCount($persistenceErrorsCount)
  {
    $this->persistenceErrorsCount = $persistenceErrorsCount;
  }
  /**
   * @return string
   */
  public function getPersistenceErrorsCount()
  {
    return $this->persistenceErrorsCount;
  }
  /**
   * Optional. Delta of errors counts from retrieving, or translating the
   * samples from previous report.
   *
   * @param string $translationErrorsCount
   */
  public function setTranslationErrorsCount($translationErrorsCount)
  {
    $this->translationErrorsCount = $translationErrorsCount;
  }
  /**
   * @return string
   */
  public function getTranslationErrorsCount()
  {
    return $this->translationErrorsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSamplingReport::class, 'Google_Service_Dataflow_DataSamplingReport');
