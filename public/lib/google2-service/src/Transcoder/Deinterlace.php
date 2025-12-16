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

namespace Google\Service\Transcoder;

class Deinterlace extends \Google\Model
{
  protected $bwdifType = BwdifConfig::class;
  protected $bwdifDataType = '';
  protected $yadifType = YadifConfig::class;
  protected $yadifDataType = '';

  /**
   * Specifies the Bob Weaver Deinterlacing Filter Configuration.
   *
   * @param BwdifConfig $bwdif
   */
  public function setBwdif(BwdifConfig $bwdif)
  {
    $this->bwdif = $bwdif;
  }
  /**
   * @return BwdifConfig
   */
  public function getBwdif()
  {
    return $this->bwdif;
  }
  /**
   * Specifies the Yet Another Deinterlacing Filter Configuration.
   *
   * @param YadifConfig $yadif
   */
  public function setYadif(YadifConfig $yadif)
  {
    $this->yadif = $yadif;
  }
  /**
   * @return YadifConfig
   */
  public function getYadif()
  {
    return $this->yadif;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Deinterlace::class, 'Google_Service_Transcoder_Deinterlace');
