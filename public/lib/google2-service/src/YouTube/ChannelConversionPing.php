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

namespace Google\Service\YouTube;

class ChannelConversionPing extends \Google\Model
{
  public const CONTEXT_subscribe = 'subscribe';
  public const CONTEXT_unsubscribe = 'unsubscribe';
  public const CONTEXT_cview = 'cview';
  /**
   * Defines the context of the ping.
   *
   * @var string
   */
  public $context;
  /**
   * The url (without the schema) that the player shall send the ping to. It's
   * at caller's descretion to decide which schema to use (http vs https)
   * Example of a returned url: //googleads.g.doubleclick.net/pagead/
   * viewthroughconversion/962985656/?data=path%3DtHe_path%3Btype%3D
   * cview%3Butuid%3DGISQtTNGYqaYl4sKxoVvKA&labe=default The caller must append
   * biscotti authentication (ms param in case of mobile, for example) to this
   * ping.
   *
   * @var string
   */
  public $conversionUrl;

  /**
   * Defines the context of the ping.
   *
   * Accepted values: subscribe, unsubscribe, cview
   *
   * @param self::CONTEXT_* $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return self::CONTEXT_*
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * The url (without the schema) that the player shall send the ping to. It's
   * at caller's descretion to decide which schema to use (http vs https)
   * Example of a returned url: //googleads.g.doubleclick.net/pagead/
   * viewthroughconversion/962985656/?data=path%3DtHe_path%3Btype%3D
   * cview%3Butuid%3DGISQtTNGYqaYl4sKxoVvKA&labe=default The caller must append
   * biscotti authentication (ms param in case of mobile, for example) to this
   * ping.
   *
   * @param string $conversionUrl
   */
  public function setConversionUrl($conversionUrl)
  {
    $this->conversionUrl = $conversionUrl;
  }
  /**
   * @return string
   */
  public function getConversionUrl()
  {
    return $this->conversionUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelConversionPing::class, 'Google_Service_YouTube_ChannelConversionPing');
