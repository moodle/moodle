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

class DicomNotificationConfig extends \Google\Model
{
  /**
   * Required. The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It is guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. * `PubsubMessage.Attributes` contains the
   * following attributes: * `action`: The name of the endpoint that generated
   * the notification. Possible values are `StoreInstances`, `SetBlobSettings`,
   * `ImportDicomData`, etc. * `lastUpdatedTime`: The latest timestamp when the
   * DICOM instance was updated. * `storeName`: The resource name of the DICOM
   * store, of the form `projects/{project_id}/locations/{location_id}/datasets/
   * {dataset_id}/dicomStores/{dicom_store_id}`. * `studyInstanceUID`: The study
   * UID of the DICOM instance that was changed. * `seriesInstanceUID`: The
   * series UID of the DICOM instance that was changed. * `sopInstanceUID`: The
   * instance UID of the DICOM instance that was changed. * `versionId`: The
   * version ID of the DICOM instance that was changed. * `modality`: The
   * modality tag of the DICOM instance that was changed. *
   * `previousStorageClass`: The storage class where the DICOM instance was
   * previously stored if the storage class was changed. * `storageClass`: The
   * storage class where the DICOM instance is currently stored. Note that
   * notifications are only sent if the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-@gcp-sa-
   * healthcare.iam.gserviceaccount.com, must have the `pubsub.topics.publish`
   * permission (which is typically included in `roles/pubsub.publisher` role)
   * on the given Pub/Sub topic. Not having adequate permissions causes the
   * calls that send notifications to fail (https://cloud.google.com/healthcare-
   * api/docs/permissions-healthcare-api-gcp-
   * products#dicom_fhir_and_hl7v2_store_cloud_pubsub_permissions). If a
   * notification can't be published to Pub/Sub, errors are logged to Cloud
   * Logging. For more information, see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare-api/docs/how-tos/logging).
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Required. The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It is guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. * `PubsubMessage.Attributes` contains the
   * following attributes: * `action`: The name of the endpoint that generated
   * the notification. Possible values are `StoreInstances`, `SetBlobSettings`,
   * `ImportDicomData`, etc. * `lastUpdatedTime`: The latest timestamp when the
   * DICOM instance was updated. * `storeName`: The resource name of the DICOM
   * store, of the form `projects/{project_id}/locations/{location_id}/datasets/
   * {dataset_id}/dicomStores/{dicom_store_id}`. * `studyInstanceUID`: The study
   * UID of the DICOM instance that was changed. * `seriesInstanceUID`: The
   * series UID of the DICOM instance that was changed. * `sopInstanceUID`: The
   * instance UID of the DICOM instance that was changed. * `versionId`: The
   * version ID of the DICOM instance that was changed. * `modality`: The
   * modality tag of the DICOM instance that was changed. *
   * `previousStorageClass`: The storage class where the DICOM instance was
   * previously stored if the storage class was changed. * `storageClass`: The
   * storage class where the DICOM instance is currently stored. Note that
   * notifications are only sent if the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-@gcp-sa-
   * healthcare.iam.gserviceaccount.com, must have the `pubsub.topics.publish`
   * permission (which is typically included in `roles/pubsub.publisher` role)
   * on the given Pub/Sub topic. Not having adequate permissions causes the
   * calls that send notifications to fail (https://cloud.google.com/healthcare-
   * api/docs/permissions-healthcare-api-gcp-
   * products#dicom_fhir_and_hl7v2_store_cloud_pubsub_permissions). If a
   * notification can't be published to Pub/Sub, errors are logged to Cloud
   * Logging. For more information, see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare-api/docs/how-tos/logging).
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DicomNotificationConfig::class, 'Google_Service_CloudHealthcare_DicomNotificationConfig');
