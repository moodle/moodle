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

namespace Google\Service\FirebaseAppHosting;

class CodebaseSource extends \Google\Model
{
  protected $authorType = UserMetadata::class;
  protected $authorDataType = '';
  /**
   * The branch in the codebase to build from, using the latest commit.
   *
   * @var string
   */
  public $branch;
  /**
   * The commit in the codebase to build from.
   *
   * @var string
   */
  public $commit;
  /**
   * Output only. The message of a codebase change.
   *
   * @var string
   */
  public $commitMessage;
  /**
   * Output only. The time the change was made.
   *
   * @var string
   */
  public $commitTime;
  /**
   * Output only. The human-friendly name to use for this Codebase when
   * displaying a build. We use the first eight characters of the SHA-1 hash for
   * GitHub.com.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The full SHA-1 hash of a Git commit, if available.
   *
   * @var string
   */
  public $hash;
  /**
   * Output only. A URI linking to the codebase on an hosting provider's
   * website. May not be valid if the commit has been rebased or force-pushed
   * out of existence in the linked repository.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The author contained in the metadata of a version control
   * change.
   *
   * @param UserMetadata $author
   */
  public function setAuthor(UserMetadata $author)
  {
    $this->author = $author;
  }
  /**
   * @return UserMetadata
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * The branch in the codebase to build from, using the latest commit.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * The commit in the codebase to build from.
   *
   * @param string $commit
   */
  public function setCommit($commit)
  {
    $this->commit = $commit;
  }
  /**
   * @return string
   */
  public function getCommit()
  {
    return $this->commit;
  }
  /**
   * Output only. The message of a codebase change.
   *
   * @param string $commitMessage
   */
  public function setCommitMessage($commitMessage)
  {
    $this->commitMessage = $commitMessage;
  }
  /**
   * @return string
   */
  public function getCommitMessage()
  {
    return $this->commitMessage;
  }
  /**
   * Output only. The time the change was made.
   *
   * @param string $commitTime
   */
  public function setCommitTime($commitTime)
  {
    $this->commitTime = $commitTime;
  }
  /**
   * @return string
   */
  public function getCommitTime()
  {
    return $this->commitTime;
  }
  /**
   * Output only. The human-friendly name to use for this Codebase when
   * displaying a build. We use the first eight characters of the SHA-1 hash for
   * GitHub.com.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The full SHA-1 hash of a Git commit, if available.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * Output only. A URI linking to the codebase on an hosting provider's
   * website. May not be valid if the commit has been rebased or force-pushed
   * out of existence in the linked repository.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CodebaseSource::class, 'Google_Service_FirebaseAppHosting_CodebaseSource');
