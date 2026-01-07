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

namespace Google\Service\AccessContextManager;

class CommitServicePerimetersRequest extends \Google\Model
{
  /**
   * Optional. The etag for the version of the Access Policy that this commit
   * operation is to be performed on. If, at the time of commit, the etag for
   * the Access Policy stored in Access Context Manager is different from the
   * specified etag, then the commit operation will not be performed and the
   * call will fail. This field is not required. If etag is not provided, the
   * operation will be performed as if a valid etag is provided.
   *
   * @var string
   */
  public $etag;

  /**
   * Optional. The etag for the version of the Access Policy that this commit
   * operation is to be performed on. If, at the time of commit, the etag for
   * the Access Policy stored in Access Context Manager is different from the
   * specified etag, then the commit operation will not be performed and the
   * call will fail. This field is not required. If etag is not provided, the
   * operation will be performed as if a valid etag is provided.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitServicePerimetersRequest::class, 'Google_Service_AccessContextManager_CommitServicePerimetersRequest');
