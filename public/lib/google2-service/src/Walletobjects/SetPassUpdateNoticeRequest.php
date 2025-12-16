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

namespace Google\Service\Walletobjects;

class SetPassUpdateNoticeRequest extends \Google\Model
{
  /**
   * Required. A fully qualified identifier of the pass that the issuer wants to
   * notify the pass holder(s) about. Formatted as .
   *
   * @var string
   */
  public $externalPassId;
  /**
   * Required. The issuer endpoint URI the pass holder needs to follow in order
   * to receive an updated pass JWT. It can not contain any sensitive
   * information. The endpoint needs to authenticate the user before giving the
   * user the updated JWT. Example update URI
   * https://someissuer.com/update/passId=someExternalPassId
   *
   * @var string
   */
  public $updateUri;
  /**
   * Required. The JWT signature of the updated pass that the issuer wants to
   * notify Google about. Only devices that report a different JWT signature
   * than this JWT signature will receive the update notification.
   *
   * @var string
   */
  public $updatedPassJwtSignature;

  /**
   * Required. A fully qualified identifier of the pass that the issuer wants to
   * notify the pass holder(s) about. Formatted as .
   *
   * @param string $externalPassId
   */
  public function setExternalPassId($externalPassId)
  {
    $this->externalPassId = $externalPassId;
  }
  /**
   * @return string
   */
  public function getExternalPassId()
  {
    return $this->externalPassId;
  }
  /**
   * Required. The issuer endpoint URI the pass holder needs to follow in order
   * to receive an updated pass JWT. It can not contain any sensitive
   * information. The endpoint needs to authenticate the user before giving the
   * user the updated JWT. Example update URI
   * https://someissuer.com/update/passId=someExternalPassId
   *
   * @param string $updateUri
   */
  public function setUpdateUri($updateUri)
  {
    $this->updateUri = $updateUri;
  }
  /**
   * @return string
   */
  public function getUpdateUri()
  {
    return $this->updateUri;
  }
  /**
   * Required. The JWT signature of the updated pass that the issuer wants to
   * notify Google about. Only devices that report a different JWT signature
   * than this JWT signature will receive the update notification.
   *
   * @param string $updatedPassJwtSignature
   */
  public function setUpdatedPassJwtSignature($updatedPassJwtSignature)
  {
    $this->updatedPassJwtSignature = $updatedPassJwtSignature;
  }
  /**
   * @return string
   */
  public function getUpdatedPassJwtSignature()
  {
    return $this->updatedPassJwtSignature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetPassUpdateNoticeRequest::class, 'Google_Service_Walletobjects_SetPassUpdateNoticeRequest');
