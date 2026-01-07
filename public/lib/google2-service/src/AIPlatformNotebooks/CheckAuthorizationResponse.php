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

namespace Google\Service\AIPlatformNotebooks;

class CheckAuthorizationResponse extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "oauthUri" => "oauth_uri",
  ];
  /**
   * Output only. Timestamp when this Authorization request was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * If the user has not completed OAuth consent, then the oauth_url is
   * returned. Otherwise, this field is not set.
   *
   * @var string
   */
  public $oauthUri;
  /**
   * Success indicates that the user completed OAuth consent and access tokens
   * can be generated.
   *
   * @var bool
   */
  public $success;

  /**
   * Output only. Timestamp when this Authorization request was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * If the user has not completed OAuth consent, then the oauth_url is
   * returned. Otherwise, this field is not set.
   *
   * @param string $oauthUri
   */
  public function setOauthUri($oauthUri)
  {
    $this->oauthUri = $oauthUri;
  }
  /**
   * @return string
   */
  public function getOauthUri()
  {
    return $this->oauthUri;
  }
  /**
   * Success indicates that the user completed OAuth consent and access tokens
   * can be generated.
   *
   * @param bool $success
   */
  public function setSuccess($success)
  {
    $this->success = $success;
  }
  /**
   * @return bool
   */
  public function getSuccess()
  {
    return $this->success;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckAuthorizationResponse::class, 'Google_Service_AIPlatformNotebooks_CheckAuthorizationResponse');
