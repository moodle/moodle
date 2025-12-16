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

class GoogleCloudRunV2VersionToPath extends \Google\Model
{
  /**
   * Integer octal mode bits to use on this file, must be a value between 01 and
   * 0777 (octal). If 0 or not set, the Volume's default mode will be used.
   * Notes * Internally, a umask of 0222 will be applied to any non-zero value.
   * * This is an integer representation of the mode bits. So, the octal integer
   * value should look exactly as the chmod numeric notation with a leading
   * zero. Some examples: for chmod 640 (u=rw,g=r), set to 0640 (octal) or 416
   * (base-10). For chmod 755 (u=rwx,g=rx,o=rx), set to 0755 (octal) or 493
   * (base-10). * This might be in conflict with other options that affect the
   * file mode, like fsGroup, and the result can be other mode bits set.
   *
   * @var int
   */
  public $mode;
  /**
   * Required. The relative path of the secret in the container.
   *
   * @var string
   */
  public $path;
  /**
   * The Cloud Secret Manager secret version. Can be 'latest' for the latest
   * value, or an integer or a secret alias for a specific version.
   *
   * @var string
   */
  public $version;

  /**
   * Integer octal mode bits to use on this file, must be a value between 01 and
   * 0777 (octal). If 0 or not set, the Volume's default mode will be used.
   * Notes * Internally, a umask of 0222 will be applied to any non-zero value.
   * * This is an integer representation of the mode bits. So, the octal integer
   * value should look exactly as the chmod numeric notation with a leading
   * zero. Some examples: for chmod 640 (u=rw,g=r), set to 0640 (octal) or 416
   * (base-10). For chmod 755 (u=rwx,g=rx,o=rx), set to 0755 (octal) or 493
   * (base-10). * This might be in conflict with other options that affect the
   * file mode, like fsGroup, and the result can be other mode bits set.
   *
   * @param int $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return int
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Required. The relative path of the secret in the container.
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
   * The Cloud Secret Manager secret version. Can be 'latest' for the latest
   * value, or an integer or a secret alias for a specific version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2VersionToPath::class, 'Google_Service_CloudRun_GoogleCloudRunV2VersionToPath');
