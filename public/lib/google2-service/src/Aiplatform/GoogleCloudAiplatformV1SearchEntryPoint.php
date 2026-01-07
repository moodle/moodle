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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SearchEntryPoint extends \Google\Model
{
  /**
   * Optional. An HTML snippet that can be embedded in a web page or an
   * application's webview. This snippet displays a search result, including the
   * title, URL, and a brief description of the search result.
   *
   * @var string
   */
  public $renderedContent;
  /**
   * Optional. A base64-encoded JSON object that contains a list of search
   * queries and their corresponding search URLs. This information can be used
   * to build a custom search UI.
   *
   * @var string
   */
  public $sdkBlob;

  /**
   * Optional. An HTML snippet that can be embedded in a web page or an
   * application's webview. This snippet displays a search result, including the
   * title, URL, and a brief description of the search result.
   *
   * @param string $renderedContent
   */
  public function setRenderedContent($renderedContent)
  {
    $this->renderedContent = $renderedContent;
  }
  /**
   * @return string
   */
  public function getRenderedContent()
  {
    return $this->renderedContent;
  }
  /**
   * Optional. A base64-encoded JSON object that contains a list of search
   * queries and their corresponding search URLs. This information can be used
   * to build a custom search UI.
   *
   * @param string $sdkBlob
   */
  public function setSdkBlob($sdkBlob)
  {
    $this->sdkBlob = $sdkBlob;
  }
  /**
   * @return string
   */
  public function getSdkBlob()
  {
    return $this->sdkBlob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchEntryPoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchEntryPoint');
