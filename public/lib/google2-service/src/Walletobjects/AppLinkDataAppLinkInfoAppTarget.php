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

namespace Google\Service\Walletobjects;

class AppLinkDataAppLinkInfoAppTarget extends \Google\Model
{
  /**
   * Package name for AppTarget. For example: com.google.android.gm
   *
   * @var string
   */
  public $packageName;
  protected $targetUriType = Uri::class;
  protected $targetUriDataType = '';

  /**
   * Package name for AppTarget. For example: com.google.android.gm
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * URI for AppTarget. The description on the URI must be set. Prefer setting
   * package field instead, if this target is defined for your application.
   *
   * @param Uri $targetUri
   */
  public function setTargetUri(Uri $targetUri)
  {
    $this->targetUri = $targetUri;
  }
  /**
   * @return Uri
   */
  public function getTargetUri()
  {
    return $this->targetUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppLinkDataAppLinkInfoAppTarget::class, 'Google_Service_Walletobjects_AppLinkDataAppLinkInfoAppTarget');
