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

namespace Google\Service\CloudTasks;

class UriOverride extends \Google\Model
{
  /**
   * Scheme unspecified. Defaults to HTTPS.
   */
  public const SCHEME_SCHEME_UNSPECIFIED = 'SCHEME_UNSPECIFIED';
  /**
   * Convert the scheme to HTTP, e.g., "https://www.example.com" will change to
   * "http://www.example.com".
   */
  public const SCHEME_HTTP = 'HTTP';
  /**
   * Convert the scheme to HTTPS, e.g., "http://www.example.com" will change to
   * "https://www.example.com".
   */
  public const SCHEME_HTTPS = 'HTTPS';
  /**
   * UriOverrideEnforceMode Unspecified. Defaults to ALWAYS.
   */
  public const URI_OVERRIDE_ENFORCE_MODE_URI_OVERRIDE_ENFORCE_MODE_UNSPECIFIED = 'URI_OVERRIDE_ENFORCE_MODE_UNSPECIFIED';
  /**
   * In the IF_NOT_EXISTS mode, queue-level configuration is only applied where
   * task-level configuration does not exist.
   */
  public const URI_OVERRIDE_ENFORCE_MODE_IF_NOT_EXISTS = 'IF_NOT_EXISTS';
  /**
   * In the ALWAYS mode, queue-level configuration overrides all task-level
   * configuration
   */
  public const URI_OVERRIDE_ENFORCE_MODE_ALWAYS = 'ALWAYS';
  /**
   * Host override. When specified, replaces the host part of the task URL. For
   * example, if the task URL is "https://www.google.com," and host value is set
   * to "example.net", the overridden URI will be changed to
   * "https://example.net." Host value cannot be an empty string
   * (INVALID_ARGUMENT).
   *
   * @var string
   */
  public $host;
  protected $pathOverrideType = PathOverride::class;
  protected $pathOverrideDataType = '';
  /**
   * Port override. When specified, replaces the port part of the task URI. For
   * instance, for a URI "https://www.example.com/example" and port=123, the
   * overridden URI becomes "https://www.example.com:123/example". Note that the
   * port value must be a positive integer. Setting the port to 0 (Zero) clears
   * the URI port.
   *
   * @var string
   */
  public $port;
  protected $queryOverrideType = QueryOverride::class;
  protected $queryOverrideDataType = '';
  /**
   * Scheme override. When specified, the task URI scheme is replaced by the
   * provided value (HTTP or HTTPS).
   *
   * @var string
   */
  public $scheme;
  /**
   * URI Override Enforce Mode When specified, determines the Target UriOverride
   * mode. If not specified, it defaults to ALWAYS.
   *
   * @var string
   */
  public $uriOverrideEnforceMode;

  /**
   * Host override. When specified, replaces the host part of the task URL. For
   * example, if the task URL is "https://www.google.com," and host value is set
   * to "example.net", the overridden URI will be changed to
   * "https://example.net." Host value cannot be an empty string
   * (INVALID_ARGUMENT).
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * URI path. When specified, replaces the existing path of the task URL.
   * Setting the path value to an empty string clears the URI path segment.
   *
   * @param PathOverride $pathOverride
   */
  public function setPathOverride(PathOverride $pathOverride)
  {
    $this->pathOverride = $pathOverride;
  }
  /**
   * @return PathOverride
   */
  public function getPathOverride()
  {
    return $this->pathOverride;
  }
  /**
   * Port override. When specified, replaces the port part of the task URI. For
   * instance, for a URI "https://www.example.com/example" and port=123, the
   * overridden URI becomes "https://www.example.com:123/example". Note that the
   * port value must be a positive integer. Setting the port to 0 (Zero) clears
   * the URI port.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * URI query. When specified, replaces the query part of the task URI. Setting
   * the query value to an empty string clears the URI query segment.
   *
   * @param QueryOverride $queryOverride
   */
  public function setQueryOverride(QueryOverride $queryOverride)
  {
    $this->queryOverride = $queryOverride;
  }
  /**
   * @return QueryOverride
   */
  public function getQueryOverride()
  {
    return $this->queryOverride;
  }
  /**
   * Scheme override. When specified, the task URI scheme is replaced by the
   * provided value (HTTP or HTTPS).
   *
   * Accepted values: SCHEME_UNSPECIFIED, HTTP, HTTPS
   *
   * @param self::SCHEME_* $scheme
   */
  public function setScheme($scheme)
  {
    $this->scheme = $scheme;
  }
  /**
   * @return self::SCHEME_*
   */
  public function getScheme()
  {
    return $this->scheme;
  }
  /**
   * URI Override Enforce Mode When specified, determines the Target UriOverride
   * mode. If not specified, it defaults to ALWAYS.
   *
   * Accepted values: URI_OVERRIDE_ENFORCE_MODE_UNSPECIFIED, IF_NOT_EXISTS,
   * ALWAYS
   *
   * @param self::URI_OVERRIDE_ENFORCE_MODE_* $uriOverrideEnforceMode
   */
  public function setUriOverrideEnforceMode($uriOverrideEnforceMode)
  {
    $this->uriOverrideEnforceMode = $uriOverrideEnforceMode;
  }
  /**
   * @return self::URI_OVERRIDE_ENFORCE_MODE_*
   */
  public function getUriOverrideEnforceMode()
  {
    return $this->uriOverrideEnforceMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UriOverride::class, 'Google_Service_CloudTasks_UriOverride');
