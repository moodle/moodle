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

namespace Google\Service\Translate;

class GcsOutputDestination extends \Google\Model
{
  /**
   * Required. Google Cloud Storage URI to output directory. For example,
   * `gs://bucket/directory`. The requesting user must have write permission to
   * the bucket. The directory will be created if it doesn't exist.
   *
   * @var string
   */
  public $outputUriPrefix;

  /**
   * Required. Google Cloud Storage URI to output directory. For example,
   * `gs://bucket/directory`. The requesting user must have write permission to
   * the bucket. The directory will be created if it doesn't exist.
   *
   * @param string $outputUriPrefix
   */
  public function setOutputUriPrefix($outputUriPrefix)
  {
    $this->outputUriPrefix = $outputUriPrefix;
  }
  /**
   * @return string
   */
  public function getOutputUriPrefix()
  {
    return $this->outputUriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcsOutputDestination::class, 'Google_Service_Translate_GcsOutputDestination');
