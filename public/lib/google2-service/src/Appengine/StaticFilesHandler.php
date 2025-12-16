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

namespace Google\Service\Appengine;

class StaticFilesHandler extends \Google\Model
{
  /**
   * Whether files should also be uploaded as code data. By default, files
   * declared in static file handlers are uploaded as static data and are only
   * served to end users; they cannot be read by the application. If enabled,
   * uploads are charged against both your code and static data storage resource
   * quotas.
   *
   * @var bool
   */
  public $applicationReadable;
  /**
   * Time a static file served by this handler should be cached by web proxies
   * and browsers.
   *
   * @var string
   */
  public $expiration;
  /**
   * HTTP headers to use for all responses from these URLs.
   *
   * @var string[]
   */
  public $httpHeaders;
  /**
   * MIME type used to serve all files served by this handler.Defaults to file-
   * specific MIME types, which are derived from each file's filename extension.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Path to the static files matched by the URL pattern, from the application
   * root directory. The path can refer to text matched in groupings in the URL
   * pattern.
   *
   * @var string
   */
  public $path;
  /**
   * Whether this handler should match the request if the file referenced by the
   * handler does not exist.
   *
   * @var bool
   */
  public $requireMatchingFile;
  /**
   * Regular expression that matches the file paths for all files that should be
   * referenced by this handler.
   *
   * @var string
   */
  public $uploadPathRegex;

  /**
   * Whether files should also be uploaded as code data. By default, files
   * declared in static file handlers are uploaded as static data and are only
   * served to end users; they cannot be read by the application. If enabled,
   * uploads are charged against both your code and static data storage resource
   * quotas.
   *
   * @param bool $applicationReadable
   */
  public function setApplicationReadable($applicationReadable)
  {
    $this->applicationReadable = $applicationReadable;
  }
  /**
   * @return bool
   */
  public function getApplicationReadable()
  {
    return $this->applicationReadable;
  }
  /**
   * Time a static file served by this handler should be cached by web proxies
   * and browsers.
   *
   * @param string $expiration
   */
  public function setExpiration($expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return string
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
  /**
   * HTTP headers to use for all responses from these URLs.
   *
   * @param string[] $httpHeaders
   */
  public function setHttpHeaders($httpHeaders)
  {
    $this->httpHeaders = $httpHeaders;
  }
  /**
   * @return string[]
   */
  public function getHttpHeaders()
  {
    return $this->httpHeaders;
  }
  /**
   * MIME type used to serve all files served by this handler.Defaults to file-
   * specific MIME types, which are derived from each file's filename extension.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Path to the static files matched by the URL pattern, from the application
   * root directory. The path can refer to text matched in groupings in the URL
   * pattern.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Whether this handler should match the request if the file referenced by the
   * handler does not exist.
   *
   * @param bool $requireMatchingFile
   */
  public function setRequireMatchingFile($requireMatchingFile)
  {
    $this->requireMatchingFile = $requireMatchingFile;
  }
  /**
   * @return bool
   */
  public function getRequireMatchingFile()
  {
    return $this->requireMatchingFile;
  }
  /**
   * Regular expression that matches the file paths for all files that should be
   * referenced by this handler.
   *
   * @param string $uploadPathRegex
   */
  public function setUploadPathRegex($uploadPathRegex)
  {
    $this->uploadPathRegex = $uploadPathRegex;
  }
  /**
   * @return string
   */
  public function getUploadPathRegex()
  {
    return $this->uploadPathRegex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StaticFilesHandler::class, 'Google_Service_Appengine_StaticFilesHandler');
