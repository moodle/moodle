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

namespace Google\Service\SecureSourceManager;

class CreatePullRequestCommentRequest extends \Google\Model
{
  /**
   * Required. The pull request in which to create the pull request comment.
   * Format: `projects/{project_number}/locations/{location_id}/repositories/{re
   * pository_id}/pullRequests/{pull_request_id}`
   *
   * @var string
   */
  public $parent;
  protected $pullRequestCommentType = PullRequestComment::class;
  protected $pullRequestCommentDataType = '';

  /**
   * Required. The pull request in which to create the pull request comment.
   * Format: `projects/{project_number}/locations/{location_id}/repositories/{re
   * pository_id}/pullRequests/{pull_request_id}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The pull request comment to create.
   *
   * @param PullRequestComment $pullRequestComment
   */
  public function setPullRequestComment(PullRequestComment $pullRequestComment)
  {
    $this->pullRequestComment = $pullRequestComment;
  }
  /**
   * @return PullRequestComment
   */
  public function getPullRequestComment()
  {
    return $this->pullRequestComment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreatePullRequestCommentRequest::class, 'Google_Service_SecureSourceManager_CreatePullRequestCommentRequest');
