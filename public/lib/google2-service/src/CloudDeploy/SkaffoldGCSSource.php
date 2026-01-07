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

namespace Google\Service\CloudDeploy;

class SkaffoldGCSSource extends \Google\Model
{
  /**
   * Optional. Relative path from the source to the Skaffold file.
   *
   * @var string
   */
  public $path;
  /**
   * Required. Cloud Storage source paths to copy recursively. For example,
   * providing "gs://my-bucket/dir/configs" will result in Skaffold copying all
   * files within the "dir/configs" directory in the bucket "my-bucket".
   *
   * @var string
   */
  public $source;

  /**
   * Optional. Relative path from the source to the Skaffold file.
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
   * Required. Cloud Storage source paths to copy recursively. For example,
   * providing "gs://my-bucket/dir/configs" will result in Skaffold copying all
   * files within the "dir/configs" directory in the bucket "my-bucket".
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SkaffoldGCSSource::class, 'Google_Service_CloudDeploy_SkaffoldGCSSource');
