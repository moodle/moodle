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

namespace Google\Service\YouTube;

class ThirdPartyLink extends \Google\Model
{
  /**
   * Etag of this resource
   *
   * @var string
   */
  public $etag;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#thirdPartyLink".
   *
   * @var string
   */
  public $kind;
  /**
   * The linking_token identifies a YouTube account and channel with which the
   * third party account is linked.
   *
   * @var string
   */
  public $linkingToken;
  protected $snippetType = ThirdPartyLinkSnippet::class;
  protected $snippetDataType = '';
  protected $statusType = ThirdPartyLinkStatus::class;
  protected $statusDataType = '';

  /**
   * Etag of this resource
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#thirdPartyLink".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The linking_token identifies a YouTube account and channel with which the
   * third party account is linked.
   *
   * @param string $linkingToken
   */
  public function setLinkingToken($linkingToken)
  {
    $this->linkingToken = $linkingToken;
  }
  /**
   * @return string
   */
  public function getLinkingToken()
  {
    return $this->linkingToken;
  }
  /**
   * The snippet object contains basic details about the third- party account
   * link.
   *
   * @param ThirdPartyLinkSnippet $snippet
   */
  public function setSnippet(ThirdPartyLinkSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return ThirdPartyLinkSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The status object contains information about the status of the link.
   *
   * @param ThirdPartyLinkStatus $status
   */
  public function setStatus(ThirdPartyLinkStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ThirdPartyLinkStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyLink::class, 'Google_Service_YouTube_ThirdPartyLink');
