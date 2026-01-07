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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1FirewallAction extends \Google\Model
{
  protected $allowType = GoogleCloudRecaptchaenterpriseV1FirewallActionAllowAction::class;
  protected $allowDataType = '';
  protected $blockType = GoogleCloudRecaptchaenterpriseV1FirewallActionBlockAction::class;
  protected $blockDataType = '';
  protected $includeRecaptchaScriptType = GoogleCloudRecaptchaenterpriseV1FirewallActionIncludeRecaptchaScriptAction::class;
  protected $includeRecaptchaScriptDataType = '';
  protected $redirectType = GoogleCloudRecaptchaenterpriseV1FirewallActionRedirectAction::class;
  protected $redirectDataType = '';
  protected $setHeaderType = GoogleCloudRecaptchaenterpriseV1FirewallActionSetHeaderAction::class;
  protected $setHeaderDataType = '';
  protected $substituteType = GoogleCloudRecaptchaenterpriseV1FirewallActionSubstituteAction::class;
  protected $substituteDataType = '';

  /**
   * The user request did not match any policy and should be allowed access to
   * the requested resource.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionAllowAction $allow
   */
  public function setAllow(GoogleCloudRecaptchaenterpriseV1FirewallActionAllowAction $allow)
  {
    $this->allow = $allow;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionAllowAction
   */
  public function getAllow()
  {
    return $this->allow;
  }
  /**
   * This action denies access to a given page. The user gets an HTTP error
   * code.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionBlockAction $block
   */
  public function setBlock(GoogleCloudRecaptchaenterpriseV1FirewallActionBlockAction $block)
  {
    $this->block = $block;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionBlockAction
   */
  public function getBlock()
  {
    return $this->block;
  }
  /**
   * This action injects reCAPTCHA JavaScript code into the HTML page returned
   * by the site backend.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionIncludeRecaptchaScriptAction $includeRecaptchaScript
   */
  public function setIncludeRecaptchaScript(GoogleCloudRecaptchaenterpriseV1FirewallActionIncludeRecaptchaScriptAction $includeRecaptchaScript)
  {
    $this->includeRecaptchaScript = $includeRecaptchaScript;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionIncludeRecaptchaScriptAction
   */
  public function getIncludeRecaptchaScript()
  {
    return $this->includeRecaptchaScript;
  }
  /**
   * This action redirects the request to a reCAPTCHA interstitial to attach a
   * token.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionRedirectAction $redirect
   */
  public function setRedirect(GoogleCloudRecaptchaenterpriseV1FirewallActionRedirectAction $redirect)
  {
    $this->redirect = $redirect;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionRedirectAction
   */
  public function getRedirect()
  {
    return $this->redirect;
  }
  /**
   * This action sets a custom header but allow the request to continue to the
   * customer backend.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionSetHeaderAction $setHeader
   */
  public function setSetHeader(GoogleCloudRecaptchaenterpriseV1FirewallActionSetHeaderAction $setHeader)
  {
    $this->setHeader = $setHeader;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionSetHeaderAction
   */
  public function getSetHeader()
  {
    return $this->setHeader;
  }
  /**
   * This action transparently serves a different page to an offending user.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallActionSubstituteAction $substitute
   */
  public function setSubstitute(GoogleCloudRecaptchaenterpriseV1FirewallActionSubstituteAction $substitute)
  {
    $this->substitute = $substitute;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallActionSubstituteAction
   */
  public function getSubstitute()
  {
    return $this->substitute;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FirewallAction::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FirewallAction');
