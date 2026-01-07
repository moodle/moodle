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

namespace Google\Service\ServiceManagement;

class MethodSettings extends \Google\Collection
{
  protected $collection_key = 'autoPopulatedFields';
  /**
   * List of top-level fields of the request message, that should be
   * automatically populated by the client libraries based on their
   * (google.api.field_info).format. Currently supported format: UUID4. Example
   * of a YAML configuration: publishing: method_settings: - selector:
   * google.example.v1.ExampleService.CreateExample auto_populated_fields: -
   * request_id
   *
   * @var string[]
   */
  public $autoPopulatedFields;
  protected $batchingType = BatchingConfigProto::class;
  protected $batchingDataType = '';
  protected $longRunningType = LongRunning::class;
  protected $longRunningDataType = '';
  /**
   * The fully qualified name of the method, for which the options below apply.
   * This is used to find the method to apply the options. Example: publishing:
   * method_settings: - selector:
   * google.storage.control.v2.StorageControl.CreateFolder # method settings for
   * CreateFolder...
   *
   * @var string
   */
  public $selector;

  /**
   * List of top-level fields of the request message, that should be
   * automatically populated by the client libraries based on their
   * (google.api.field_info).format. Currently supported format: UUID4. Example
   * of a YAML configuration: publishing: method_settings: - selector:
   * google.example.v1.ExampleService.CreateExample auto_populated_fields: -
   * request_id
   *
   * @param string[] $autoPopulatedFields
   */
  public function setAutoPopulatedFields($autoPopulatedFields)
  {
    $this->autoPopulatedFields = $autoPopulatedFields;
  }
  /**
   * @return string[]
   */
  public function getAutoPopulatedFields()
  {
    return $this->autoPopulatedFields;
  }
  /**
   * Batching configuration for an API method in client libraries. Example of a
   * YAML configuration: publishing: method_settings: - selector:
   * google.example.v1.ExampleService.BatchCreateExample batching:
   * element_count_threshold: 1000 request_byte_threshold: 100000000
   * delay_threshold_millis: 10
   *
   * @param BatchingConfigProto $batching
   */
  public function setBatching(BatchingConfigProto $batching)
  {
    $this->batching = $batching;
  }
  /**
   * @return BatchingConfigProto
   */
  public function getBatching()
  {
    return $this->batching;
  }
  /**
   * Describes settings to use for long-running operations when generating API
   * methods for RPCs. Complements RPCs that use the annotations in
   * google/longrunning/operations.proto. Example of a YAML configuration::
   * publishing: method_settings: - selector:
   * google.cloud.speech.v2.Speech.BatchRecognize long_running:
   * initial_poll_delay: 60s # 1 minute poll_delay_multiplier: 1.5
   * max_poll_delay: 360s # 6 minutes total_poll_timeout: 54000s # 90 minutes
   *
   * @param LongRunning $longRunning
   */
  public function setLongRunning(LongRunning $longRunning)
  {
    $this->longRunning = $longRunning;
  }
  /**
   * @return LongRunning
   */
  public function getLongRunning()
  {
    return $this->longRunning;
  }
  /**
   * The fully qualified name of the method, for which the options below apply.
   * This is used to find the method to apply the options. Example: publishing:
   * method_settings: - selector:
   * google.storage.control.v2.StorageControl.CreateFolder # method settings for
   * CreateFolder...
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MethodSettings::class, 'Google_Service_ServiceManagement_MethodSettings');
