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

namespace Google\Service\Drive;

class AccessProposal extends \Google\Collection
{
  protected $collection_key = 'rolesAndViews';
  /**
   * The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * The file ID that the proposal for access is on.
   *
   * @var string
   */
  public $fileId;
  /**
   * The ID of the access proposal.
   *
   * @var string
   */
  public $proposalId;
  /**
   * The email address of the user that will receive permissions, if accepted.
   *
   * @var string
   */
  public $recipientEmailAddress;
  /**
   * The message that the requester added to the proposal.
   *
   * @var string
   */
  public $requestMessage;
  /**
   * The email address of the requesting user.
   *
   * @var string
   */
  public $requesterEmailAddress;
  protected $rolesAndViewsType = AccessProposalRoleAndView::class;
  protected $rolesAndViewsDataType = 'array';

  /**
   * The creation time.
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
   * The file ID that the proposal for access is on.
   *
   * @param string $fileId
   */
  public function setFileId($fileId)
  {
    $this->fileId = $fileId;
  }
  /**
   * @return string
   */
  public function getFileId()
  {
    return $this->fileId;
  }
  /**
   * The ID of the access proposal.
   *
   * @param string $proposalId
   */
  public function setProposalId($proposalId)
  {
    $this->proposalId = $proposalId;
  }
  /**
   * @return string
   */
  public function getProposalId()
  {
    return $this->proposalId;
  }
  /**
   * The email address of the user that will receive permissions, if accepted.
   *
   * @param string $recipientEmailAddress
   */
  public function setRecipientEmailAddress($recipientEmailAddress)
  {
    $this->recipientEmailAddress = $recipientEmailAddress;
  }
  /**
   * @return string
   */
  public function getRecipientEmailAddress()
  {
    return $this->recipientEmailAddress;
  }
  /**
   * The message that the requester added to the proposal.
   *
   * @param string $requestMessage
   */
  public function setRequestMessage($requestMessage)
  {
    $this->requestMessage = $requestMessage;
  }
  /**
   * @return string
   */
  public function getRequestMessage()
  {
    return $this->requestMessage;
  }
  /**
   * The email address of the requesting user.
   *
   * @param string $requesterEmailAddress
   */
  public function setRequesterEmailAddress($requesterEmailAddress)
  {
    $this->requesterEmailAddress = $requesterEmailAddress;
  }
  /**
   * @return string
   */
  public function getRequesterEmailAddress()
  {
    return $this->requesterEmailAddress;
  }
  /**
   * A wrapper for the role and view of an access proposal. For more
   * information, see [Roles and
   * permissions](https://developers.google.com/workspace/drive/api/guides/ref-
   * roles).
   *
   * @param AccessProposalRoleAndView[] $rolesAndViews
   */
  public function setRolesAndViews($rolesAndViews)
  {
    $this->rolesAndViews = $rolesAndViews;
  }
  /**
   * @return AccessProposalRoleAndView[]
   */
  public function getRolesAndViews()
  {
    return $this->rolesAndViews;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessProposal::class, 'Google_Service_Drive_AccessProposal');
