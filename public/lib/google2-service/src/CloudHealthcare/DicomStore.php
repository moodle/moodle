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

namespace Google\Service\CloudHealthcare;

class DicomStore extends \Google\Collection
{
  protected $collection_key = 'streamConfigs';
  /**
   * User-supplied key-value pairs used to organize DICOM stores. Label keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Resource name of the DICOM store, of the form `projects/{projec
   * t_id}/locations/{location_id}/datasets/{dataset_id}/dicomStores/{dicom_stor
   * e_id}`.
   *
   * @var string
   */
  public $name;
  protected $notificationConfigType = NotificationConfig::class;
  protected $notificationConfigDataType = '';
  protected $notificationConfigsType = DicomNotificationConfig::class;
  protected $notificationConfigsDataType = 'array';
  protected $streamConfigsType = GoogleCloudHealthcareV1DicomStreamConfig::class;
  protected $streamConfigsDataType = 'array';

  /**
   * User-supplied key-value pairs used to organize DICOM stores. Label keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
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
   * Identifier. Resource name of the DICOM store, of the form `projects/{projec
   * t_id}/locations/{location_id}/datasets/{dataset_id}/dicomStores/{dicom_stor
   * e_id}`.
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
   * Optional. Notification destination for new DICOM instances. Supplied by the
   * client.
   *
   * @param NotificationConfig $notificationConfig
   */
  public function setNotificationConfig(NotificationConfig $notificationConfig)
  {
    $this->notificationConfig = $notificationConfig;
  }
  /**
   * @return NotificationConfig
   */
  public function getNotificationConfig()
  {
    return $this->notificationConfig;
  }
  /**
   * Optional. Specifies where and whether to send notifications upon changes to
   * a DICOM store.
   *
   * @param DicomNotificationConfig[] $notificationConfigs
   */
  public function setNotificationConfigs($notificationConfigs)
  {
    $this->notificationConfigs = $notificationConfigs;
  }
  /**
   * @return DicomNotificationConfig[]
   */
  public function getNotificationConfigs()
  {
    return $this->notificationConfigs;
  }
  /**
   * Optional. A list of streaming configs used to configure the destination of
   * streaming exports for every DICOM instance insertion in this DICOM store.
   * After a new config is added to `stream_configs`, DICOM instance insertions
   * are streamed to the new destination. When a config is removed from
   * `stream_configs`, the server stops streaming to that destination. Each
   * config must contain a unique destination.
   *
   * @param GoogleCloudHealthcareV1DicomStreamConfig[] $streamConfigs
   */
  public function setStreamConfigs($streamConfigs)
  {
    $this->streamConfigs = $streamConfigs;
  }
  /**
   * @return GoogleCloudHealthcareV1DicomStreamConfig[]
   */
  public function getStreamConfigs()
  {
    return $this->streamConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DicomStore::class, 'Google_Service_CloudHealthcare_DicomStore');
