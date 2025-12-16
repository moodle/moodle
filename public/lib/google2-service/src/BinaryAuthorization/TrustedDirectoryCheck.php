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

namespace Google\Service\BinaryAuthorization;

class TrustedDirectoryCheck extends \Google\Collection
{
  protected $collection_key = 'trustedDirPatterns';
  /**
   * Required. List of trusted directory patterns. A pattern is in the form
   * "registry/path/to/directory". The registry domain part is defined as two or
   * more dot-separated words, e.g., `us.pkg.dev`, or `gcr.io`. Additionally,
   * `*` can be used in three ways as wildcards: 1. leading `*` to match varying
   * prefixes in registry subdomain (useful for location prefixes); 2. trailing
   * `*` after registry/ to match varying endings; 3. trailing `**` after
   * registry/ to match "/" as well. For example: -- `gcr.io/my-project/my-repo`
   * is valid to match a single directory -- `*-docker.pkg.dev/my-project/my-
   * repo` or `*.gcr.io/my-project` are valid to match varying prefixes --
   * `gcr.io/my-project` will match all direct directories in `my-project` --
   * `gcr.io/my-project*` would match all directories in `my-project` --
   * `gcr.i*` is not allowed since the registry is not completely specified --
   * `sub*domain.gcr.io/nginx` is not valid because only leading `*` or trailing
   * `*` are allowed. -- `*pkg.dev/my-project/my-repo` is not valid because
   * leading `*` can only match subdomain -- `**-docker.pkg.dev` is not valid
   * because one leading `*` is allowed, and that it cannot match `/`
   *
   * @var string[]
   */
  public $trustedDirPatterns;

  /**
   * Required. List of trusted directory patterns. A pattern is in the form
   * "registry/path/to/directory". The registry domain part is defined as two or
   * more dot-separated words, e.g., `us.pkg.dev`, or `gcr.io`. Additionally,
   * `*` can be used in three ways as wildcards: 1. leading `*` to match varying
   * prefixes in registry subdomain (useful for location prefixes); 2. trailing
   * `*` after registry/ to match varying endings; 3. trailing `**` after
   * registry/ to match "/" as well. For example: -- `gcr.io/my-project/my-repo`
   * is valid to match a single directory -- `*-docker.pkg.dev/my-project/my-
   * repo` or `*.gcr.io/my-project` are valid to match varying prefixes --
   * `gcr.io/my-project` will match all direct directories in `my-project` --
   * `gcr.io/my-project*` would match all directories in `my-project` --
   * `gcr.i*` is not allowed since the registry is not completely specified --
   * `sub*domain.gcr.io/nginx` is not valid because only leading `*` or trailing
   * `*` are allowed. -- `*pkg.dev/my-project/my-repo` is not valid because
   * leading `*` can only match subdomain -- `**-docker.pkg.dev` is not valid
   * because one leading `*` is allowed, and that it cannot match `/`
   *
   * @param string[] $trustedDirPatterns
   */
  public function setTrustedDirPatterns($trustedDirPatterns)
  {
    $this->trustedDirPatterns = $trustedDirPatterns;
  }
  /**
   * @return string[]
   */
  public function getTrustedDirPatterns()
  {
    return $this->trustedDirPatterns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrustedDirectoryCheck::class, 'Google_Service_BinaryAuthorization_TrustedDirectoryCheck');
