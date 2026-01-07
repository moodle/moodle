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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1GetAsyncQueryResultUrlResponseURLInfo extends \Google\Model
{
  /**
   * The MD5 Hash of the JSON data
   *
   * @var string
   */
  public $md5;
  /**
   * The size of the returned file in bytes
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * The signed URL of the JSON data. Will be of the form
   * `https://storage.googleapis.com/example-bucket/cat.jpeg?X-Goog-Algorithm=
   * GOOG4-RSA-SHA256&X-Goog-Credential=example%40example-
   * project.iam.gserviceaccount .com%2F20181026%2Fus-
   * central1%2Fstorage%2Fgoog4_request&X-Goog-Date=20181026T18 1309Z&X-Goog-
   * Expires=900&X-Goog-SignedHeaders=host&X-Goog-Signature=247a2aa45f16 9edf4d1
   * 87d54e7cc46e4731b1e6273242c4f4c39a1d2507a0e58706e25e3a85a7dbb891d62afa849 6
   * def8e260c1db863d9ace85ff0a184b894b117fe46d1225c82f2aa19efd52cf21d3e2022b3b8
   * 68dc c1aca2741951ed5bf3bb25a34f5e9316a2841e8ff4c530b22ceaa1c5ce09c7cbb57326
   * 31510c2058 0e61723f5594de3aea497f195456a2ff2bdd0d13bad47289d8611b6f9cfeef0c
   * 46c91a455b94e90a 66924f722292d21e24d31dcfb38ce0c0f353ffa5a9756fc2a9f2b40bc2
   * 113206a81e324fc4fd6823 a29163fa845c8ae7eca1fcf6e5bb48b3200983c56c5ca81fffb1
   * 51cca7402beddfc4a76b13344703 2ea7abedc098d2eb14a7`
   *
   * @var string
   */
  public $uri;

  /**
   * The MD5 Hash of the JSON data
   *
   * @param string $md5
   */
  public function setMd5($md5)
  {
    $this->md5 = $md5;
  }
  /**
   * @return string
   */
  public function getMd5()
  {
    return $this->md5;
  }
  /**
   * The size of the returned file in bytes
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * The signed URL of the JSON data. Will be of the form
   * `https://storage.googleapis.com/example-bucket/cat.jpeg?X-Goog-Algorithm=
   * GOOG4-RSA-SHA256&X-Goog-Credential=example%40example-
   * project.iam.gserviceaccount .com%2F20181026%2Fus-
   * central1%2Fstorage%2Fgoog4_request&X-Goog-Date=20181026T18 1309Z&X-Goog-
   * Expires=900&X-Goog-SignedHeaders=host&X-Goog-Signature=247a2aa45f16 9edf4d1
   * 87d54e7cc46e4731b1e6273242c4f4c39a1d2507a0e58706e25e3a85a7dbb891d62afa849 6
   * def8e260c1db863d9ace85ff0a184b894b117fe46d1225c82f2aa19efd52cf21d3e2022b3b8
   * 68dc c1aca2741951ed5bf3bb25a34f5e9316a2841e8ff4c530b22ceaa1c5ce09c7cbb57326
   * 31510c2058 0e61723f5594de3aea497f195456a2ff2bdd0d13bad47289d8611b6f9cfeef0c
   * 46c91a455b94e90a 66924f722292d21e24d31dcfb38ce0c0f353ffa5a9756fc2a9f2b40bc2
   * 113206a81e324fc4fd6823 a29163fa845c8ae7eca1fcf6e5bb48b3200983c56c5ca81fffb1
   * 51cca7402beddfc4a76b13344703 2ea7abedc098d2eb14a7`
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
class_alias(GoogleCloudApigeeV1GetAsyncQueryResultUrlResponseURLInfo::class, 'Google_Service_Apigee_GoogleCloudApigeeV1GetAsyncQueryResultUrlResponseURLInfo');
