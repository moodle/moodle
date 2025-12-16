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

namespace Google\Service\Compute;

class InterconnectMacsecConfig extends \Google\Collection
{
  protected $collection_key = 'preSharedKeys';
  protected $preSharedKeysType = InterconnectMacsecConfigPreSharedKey::class;
  protected $preSharedKeysDataType = 'array';

  /**
   * A keychain placeholder describing a set of named key objects along with
   * their start times. A MACsec CKN/CAK is generated for each key in the key
   * chain. Google router automatically picks the key with the most recent
   * startTime when establishing or re-establishing a MACsec secure link.
   *
   * @param InterconnectMacsecConfigPreSharedKey[] $preSharedKeys
   */
  public function setPreSharedKeys($preSharedKeys)
  {
    $this->preSharedKeys = $preSharedKeys;
  }
  /**
   * @return InterconnectMacsecConfigPreSharedKey[]
   */
  public function getPreSharedKeys()
  {
    return $this->preSharedKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectMacsecConfig::class, 'Google_Service_Compute_InterconnectMacsecConfig');
