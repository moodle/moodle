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

namespace Google\Service\AndroidManagement;

class ChoosePrivateKeyRule extends \Google\Collection
{
  protected $collection_key = 'packageNames';
  /**
   * The package names to which this rule applies. The signing key certificate
   * fingerprint of the app is verified against the signing key certificate
   * fingerprints provided by Play Store and ApplicationPolicy.signingKeyCerts .
   * If no package names are specified, then the alias is provided to all apps
   * that call KeyChain.choosePrivateKeyAlias (https://developer.android.com/ref
   * erence/android/security/KeyChain#choosePrivateKeyAlias%28android.app.Activi
   * ty,%20android.security.KeyChainAliasCallback,%20java.lang.String[],%20java.
   * security.Principal[],%20java.lang.String,%20int,%20java.lang.String%29) or
   * any overloads (but not without calling KeyChain.choosePrivateKeyAlias, even
   * on Android 11 and above). Any app with the same Android UID as a package
   * specified here will have access when they call
   * KeyChain.choosePrivateKeyAlias.
   *
   * @var string[]
   */
  public $packageNames;
  /**
   * The alias of the private key to be used.
   *
   * @var string
   */
  public $privateKeyAlias;
  /**
   * The URL pattern to match against the URL of the request. If not set or
   * empty, it matches all URLs. This uses the regular expression syntax of
   * java.util.regex.Pattern.
   *
   * @var string
   */
  public $urlPattern;

  /**
   * The package names to which this rule applies. The signing key certificate
   * fingerprint of the app is verified against the signing key certificate
   * fingerprints provided by Play Store and ApplicationPolicy.signingKeyCerts .
   * If no package names are specified, then the alias is provided to all apps
   * that call KeyChain.choosePrivateKeyAlias (https://developer.android.com/ref
   * erence/android/security/KeyChain#choosePrivateKeyAlias%28android.app.Activi
   * ty,%20android.security.KeyChainAliasCallback,%20java.lang.String[],%20java.
   * security.Principal[],%20java.lang.String,%20int,%20java.lang.String%29) or
   * any overloads (but not without calling KeyChain.choosePrivateKeyAlias, even
   * on Android 11 and above). Any app with the same Android UID as a package
   * specified here will have access when they call
   * KeyChain.choosePrivateKeyAlias.
   *
   * @param string[] $packageNames
   */
  public function setPackageNames($packageNames)
  {
    $this->packageNames = $packageNames;
  }
  /**
   * @return string[]
   */
  public function getPackageNames()
  {
    return $this->packageNames;
  }
  /**
   * The alias of the private key to be used.
   *
   * @param string $privateKeyAlias
   */
  public function setPrivateKeyAlias($privateKeyAlias)
  {
    $this->privateKeyAlias = $privateKeyAlias;
  }
  /**
   * @return string
   */
  public function getPrivateKeyAlias()
  {
    return $this->privateKeyAlias;
  }
  /**
   * The URL pattern to match against the URL of the request. If not set or
   * empty, it matches all URLs. This uses the regular expression syntax of
   * java.util.regex.Pattern.
   *
   * @param string $urlPattern
   */
  public function setUrlPattern($urlPattern)
  {
    $this->urlPattern = $urlPattern;
  }
  /**
   * @return string
   */
  public function getUrlPattern()
  {
    return $this->urlPattern;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChoosePrivateKeyRule::class, 'Google_Service_AndroidManagement_ChoosePrivateKeyRule');
