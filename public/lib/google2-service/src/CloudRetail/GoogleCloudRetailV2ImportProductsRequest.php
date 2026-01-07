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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ImportProductsRequest extends \Google\Model
{
  /**
   * Defaults to INCREMENTAL.
   */
  public const RECONCILIATION_MODE_RECONCILIATION_MODE_UNSPECIFIED = 'RECONCILIATION_MODE_UNSPECIFIED';
  /**
   * Inserts new products or updates existing products.
   */
  public const RECONCILIATION_MODE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Calculates diff and replaces the entire product dataset. Existing products
   * may be deleted if they are not present in the source location.
   */
  public const RECONCILIATION_MODE_FULL = 'FULL';
  protected $errorsConfigType = GoogleCloudRetailV2ImportErrorsConfig::class;
  protected $errorsConfigDataType = '';
  protected $inputConfigType = GoogleCloudRetailV2ProductInputConfig::class;
  protected $inputConfigDataType = '';
  /**
   * Full Pub/Sub topic name for receiving notification. If this field is set,
   * when the import is finished, a notification is sent to specified Pub/Sub
   * topic. The message data is JSON string of a Operation. Format of the
   * Pub/Sub topic is `projects/{project}/topics/{topic}`. It has to be within
   * the same project as ImportProductsRequest.parent. Make sure that both
   * `cloud-retail-customer-data-access@system.gserviceaccount.com` and
   * `service-@gcp-sa-retail.iam.gserviceaccount.com` have the
   * `pubsub.topics.publish` IAM permission on the topic. Only supported when
   * ImportProductsRequest.reconciliation_mode is set to `FULL`.
   *
   * @var string
   */
  public $notificationPubsubTopic;
  /**
   * The mode of reconciliation between existing products and the products to be
   * imported. Defaults to ReconciliationMode.INCREMENTAL.
   *
   * @var string
   */
  public $reconciliationMode;
  /**
   * Deprecated. This field has no effect.
   *
   * @deprecated
   * @var string
   */
  public $requestId;
  /**
   * Indicates which fields in the provided imported `products` to update. If
   * not set, all fields are updated. If provided, only the existing product
   * fields are updated. Missing products will not be created.
   *
   * @var string
   */
  public $updateMask;

  /**
   * The desired location of errors incurred during the Import.
   *
   * @param GoogleCloudRetailV2ImportErrorsConfig $errorsConfig
   */
  public function setErrorsConfig(GoogleCloudRetailV2ImportErrorsConfig $errorsConfig)
  {
    $this->errorsConfig = $errorsConfig;
  }
  /**
   * @return GoogleCloudRetailV2ImportErrorsConfig
   */
  public function getErrorsConfig()
  {
    return $this->errorsConfig;
  }
  /**
   * Required. The desired input location of the data.
   *
   * @param GoogleCloudRetailV2ProductInputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudRetailV2ProductInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudRetailV2ProductInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Full Pub/Sub topic name for receiving notification. If this field is set,
   * when the import is finished, a notification is sent to specified Pub/Sub
   * topic. The message data is JSON string of a Operation. Format of the
   * Pub/Sub topic is `projects/{project}/topics/{topic}`. It has to be within
   * the same project as ImportProductsRequest.parent. Make sure that both
   * `cloud-retail-customer-data-access@system.gserviceaccount.com` and
   * `service-@gcp-sa-retail.iam.gserviceaccount.com` have the
   * `pubsub.topics.publish` IAM permission on the topic. Only supported when
   * ImportProductsRequest.reconciliation_mode is set to `FULL`.
   *
   * @param string $notificationPubsubTopic
   */
  public function setNotificationPubsubTopic($notificationPubsubTopic)
  {
    $this->notificationPubsubTopic = $notificationPubsubTopic;
  }
  /**
   * @return string
   */
  public function getNotificationPubsubTopic()
  {
    return $this->notificationPubsubTopic;
  }
  /**
   * The mode of reconciliation between existing products and the products to be
   * imported. Defaults to ReconciliationMode.INCREMENTAL.
   *
   * Accepted values: RECONCILIATION_MODE_UNSPECIFIED, INCREMENTAL, FULL
   *
   * @param self::RECONCILIATION_MODE_* $reconciliationMode
   */
  public function setReconciliationMode($reconciliationMode)
  {
    $this->reconciliationMode = $reconciliationMode;
  }
  /**
   * @return self::RECONCILIATION_MODE_*
   */
  public function getReconciliationMode()
  {
    return $this->reconciliationMode;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @deprecated
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Indicates which fields in the provided imported `products` to update. If
   * not set, all fields are updated. If provided, only the existing product
   * fields are updated. Missing products will not be created.
   *
   * @param string $updateMask
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
class_alias(GoogleCloudRetailV2ImportProductsRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ImportProductsRequest');
