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

class FhirNotificationConfig extends \Google\Model
{
  /**
   * Optional. The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It is guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. Note that notifications are only sent if
   * the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-@gcp-sa-
   * healthcare.iam.gserviceaccount.com, must have publisher permissions on the
   * given Pub/Sub topic. Not having adequate permissions causes the calls that
   * send notifications to fail (https://cloud.google.com/healthcare-
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
   * Optional. Whether to send full FHIR resource to this Pub/Sub topic. The
   * default value is false.
   *
   * @var bool
   */
  public $sendFullResource;
  /**
   * Optional. Whether to send full FHIR resource to this Pub/Sub topic for
   * deleting FHIR resource. The default value is false. Note that setting this
   * to true does not guarantee that all previous resources will be sent in the
   * format of full FHIR resource. When a resource change is too large or during
   * heavy traffic, only the resource name will be sent. Clients should always
   * check the "payloadType" label from a Pub/Sub message to determine whether
   * it needs to fetch the full previous resource as a separate operation.
   *
   * @var bool
   */
  public $sendPreviousResourceOnDelete;

  /**
   * Optional. The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It is guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. Note that notifications are only sent if
   * the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-@gcp-sa-
   * healthcare.iam.gserviceaccount.com, must have publisher permissions on the
   * given Pub/Sub topic. Not having adequate permissions causes the calls that
   * send notifications to fail (https://cloud.google.com/healthcare-
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
  /**
   * Optional. Whether to send full FHIR resource to this Pub/Sub topic. The
   * default value is false.
   *
   * @param bool $sendFullResource
   */
  public function setSendFullResource($sendFullResource)
  {
    $this->sendFullResource = $sendFullResource;
  }
  /**
   * @return bool
   */
  public function getSendFullResource()
  {
    return $this->sendFullResource;
  }
  /**
   * Optional. Whether to send full FHIR resource to this Pub/Sub topic for
   * deleting FHIR resource. The default value is false. Note that setting this
   * to true does not guarantee that all previous resources will be sent in the
   * format of full FHIR resource. When a resource change is too large or during
   * heavy traffic, only the resource name will be sent. Clients should always
   * check the "payloadType" label from a Pub/Sub message to determine whether
   * it needs to fetch the full previous resource as a separate operation.
   *
   * @param bool $sendPreviousResourceOnDelete
   */
  public function setSendPreviousResourceOnDelete($sendPreviousResourceOnDelete)
  {
    $this->sendPreviousResourceOnDelete = $sendPreviousResourceOnDelete;
  }
  /**
   * @return bool
   */
  public function getSendPreviousResourceOnDelete()
  {
    return $this->sendPreviousResourceOnDelete;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FhirNotificationConfig::class, 'Google_Service_CloudHealthcare_FhirNotificationConfig');
