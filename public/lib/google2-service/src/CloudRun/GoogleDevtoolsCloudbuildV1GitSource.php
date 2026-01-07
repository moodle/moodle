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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1GitSource extends \Google\Model
{
  /**
   * Optional. Directory, relative to the source root, in which to run the
   * build. This must be a relative path. If a step's `dir` is specified and is
   * an absolute path, this value is ignored for that step's execution.
   *
   * @var string
   */
  public $dir;
  /**
   * Optional. The revision to fetch from the Git repository such as a branch, a
   * tag, a commit SHA, or any Git ref. Cloud Build uses `git fetch` to fetch
   * the revision from the Git repository; therefore make sure that the string
   * you provide for `revision` is parsable by the command. For information on
   * string values accepted by `git fetch`, see https://git-
   * scm.com/docs/gitrevisions#_specifying_revisions. For information on `git
   * fetch`, see https://git-scm.com/docs/git-fetch.
   *
   * @var string
   */
  public $revision;
  /**
   * Required. Location of the Git repo to build. This will be used as a `git
   * remote`, see https://git-scm.com/docs/git-remote.
   *
   * @var string
   */
  public $url;

  /**
   * Optional. Directory, relative to the source root, in which to run the
   * build. This must be a relative path. If a step's `dir` is specified and is
   * an absolute path, this value is ignored for that step's execution.
   *
   * @param string $dir
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
  }
  /**
   * @return string
   */
  public function getDir()
  {
    return $this->dir;
  }
  /**
   * Optional. The revision to fetch from the Git repository such as a branch, a
   * tag, a commit SHA, or any Git ref. Cloud Build uses `git fetch` to fetch
   * the revision from the Git repository; therefore make sure that the string
   * you provide for `revision` is parsable by the command. For information on
   * string values accepted by `git fetch`, see https://git-
   * scm.com/docs/gitrevisions#_specifying_revisions. For information on `git
   * fetch`, see https://git-scm.com/docs/git-fetch.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Required. Location of the Git repo to build. This will be used as a `git
   * remote`, see https://git-scm.com/docs/git-remote.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1GitSource::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1GitSource');
