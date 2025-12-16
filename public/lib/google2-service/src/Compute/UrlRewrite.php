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

namespace Google\Service\Compute;

class UrlRewrite extends \Google\Model
{
  /**
   * Before forwarding the request to the selected service, the request's host
   * header is replaced with contents of hostRewrite.
   *
   * The value must be from 1 to 255 characters.
   *
   * @var string
   */
  public $hostRewrite;
  /**
   * Before forwarding the request to the selected backend service, the matching
   * portion of the request's path is replaced bypathPrefixRewrite.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @var string
   */
  public $pathPrefixRewrite;
  /**
   * If specified, the pattern rewrites the URL path (based on the :path header)
   * using the HTTP template syntax.
   *
   * A corresponding path_template_match must be specified. Any template
   * variables must exist in the path_template_match field.                - -At
   * least one variable must be specified in the path_template_match       field
   * - You can omit variables from the rewritten URL       - The * and **
   * operators cannot be matched       unless they have a corresponding variable
   * name - e.g.       {format=*} or {var=**}.
   *
   * For example, a path_template_match of /static/{format=**} could be
   * rewritten as /static/content/{format} to prefix/content to the URL.
   * Variables can also be re-ordered in a rewrite, so that
   * /{country}/{format}/{suffix=**} can be rewritten as
   * /content/{format}/{country}/{suffix}.
   *
   * At least one non-empty routeRules[].matchRules[].path_template_match is
   * required.
   *
   * Only one of path_prefix_rewrite orpath_template_rewrite may be specified.
   *
   * @var string
   */
  public $pathTemplateRewrite;

  /**
   * Before forwarding the request to the selected service, the request's host
   * header is replaced with contents of hostRewrite.
   *
   * The value must be from 1 to 255 characters.
   *
   * @param string $hostRewrite
   */
  public function setHostRewrite($hostRewrite)
  {
    $this->hostRewrite = $hostRewrite;
  }
  /**
   * @return string
   */
  public function getHostRewrite()
  {
    return $this->hostRewrite;
  }
  /**
   * Before forwarding the request to the selected backend service, the matching
   * portion of the request's path is replaced bypathPrefixRewrite.
   *
   * The value must be from 1 to 1024 characters.
   *
   * @param string $pathPrefixRewrite
   */
  public function setPathPrefixRewrite($pathPrefixRewrite)
  {
    $this->pathPrefixRewrite = $pathPrefixRewrite;
  }
  /**
   * @return string
   */
  public function getPathPrefixRewrite()
  {
    return $this->pathPrefixRewrite;
  }
  /**
   * If specified, the pattern rewrites the URL path (based on the :path header)
   * using the HTTP template syntax.
   *
   * A corresponding path_template_match must be specified. Any template
   * variables must exist in the path_template_match field.                - -At
   * least one variable must be specified in the path_template_match       field
   * - You can omit variables from the rewritten URL       - The * and **
   * operators cannot be matched       unless they have a corresponding variable
   * name - e.g.       {format=*} or {var=**}.
   *
   * For example, a path_template_match of /static/{format=**} could be
   * rewritten as /static/content/{format} to prefix/content to the URL.
   * Variables can also be re-ordered in a rewrite, so that
   * /{country}/{format}/{suffix=**} can be rewritten as
   * /content/{format}/{country}/{suffix}.
   *
   * At least one non-empty routeRules[].matchRules[].path_template_match is
   * required.
   *
   * Only one of path_prefix_rewrite orpath_template_rewrite may be specified.
   *
   * @param string $pathTemplateRewrite
   */
  public function setPathTemplateRewrite($pathTemplateRewrite)
  {
    $this->pathTemplateRewrite = $pathTemplateRewrite;
  }
  /**
   * @return string
   */
  public function getPathTemplateRewrite()
  {
    return $this->pathTemplateRewrite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlRewrite::class, 'Google_Service_Compute_UrlRewrite');
