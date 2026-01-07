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

namespace Google\Service\Pubsub;

class MessageTransform extends \Google\Model
{
  /**
   * Optional. If true, the transform is disabled and will not be applied to
   * messages. Defaults to `false`.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. This field is deprecated, use the `disabled` field to disable
   * transforms.
   *
   * @deprecated
   * @var bool
   */
  public $enabled;
  protected $javascriptUdfType = JavaScriptUDF::class;
  protected $javascriptUdfDataType = '';

  /**
   * Optional. If true, the transform is disabled and will not be applied to
   * messages. Defaults to `false`.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. This field is deprecated, use the `disabled` field to disable
   * transforms.
   *
   * @deprecated
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. JavaScript User Defined Function. If multiple JavaScriptUDF's are
   * specified on a resource, each must have a unique `function_name`.
   *
   * @param JavaScriptUDF $javascriptUdf
   */
  public function setJavascriptUdf(JavaScriptUDF $javascriptUdf)
  {
    $this->javascriptUdf = $javascriptUdf;
  }
  /**
   * @return JavaScriptUDF
   */
  public function getJavascriptUdf()
  {
    return $this->javascriptUdf;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MessageTransform::class, 'Google_Service_Pubsub_MessageTransform');
