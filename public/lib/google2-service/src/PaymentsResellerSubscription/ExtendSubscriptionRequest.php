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

namespace Google\Service\PaymentsResellerSubscription;

class ExtendSubscriptionRequest extends \Google\Model
{
  protected $extensionType = Extension::class;
  protected $extensionDataType = '';
  /**
   * Required. Restricted to 36 ASCII characters. A random UUID is recommended.
   * The idempotency key for the request. The ID generation logic is controlled
   * by the partner. request_id should be the same as on retries of the same
   * request. A different request_id must be used for a extension of a different
   * cycle.
   *
   * @var string
   */
  public $requestId;

  /**
   * Required. Specifies details of the extension. Currently, the duration of
   * the extension must be exactly one billing cycle of the original
   * subscription.
   *
   * @param Extension $extension
   */
  public function setExtension(Extension $extension)
  {
    $this->extension = $extension;
  }
  /**
   * @return Extension
   */
  public function getExtension()
  {
    return $this->extension;
  }
  /**
   * Required. Restricted to 36 ASCII characters. A random UUID is recommended.
   * The idempotency key for the request. The ID generation logic is controlled
   * by the partner. request_id should be the same as on retries of the same
   * request. A different request_id must be used for a extension of a different
   * cycle.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtendSubscriptionRequest::class, 'Google_Service_PaymentsResellerSubscription_ExtendSubscriptionRequest');
