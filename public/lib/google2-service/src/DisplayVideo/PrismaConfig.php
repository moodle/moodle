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

namespace Google\Service\DisplayVideo;

class PrismaConfig extends \Google\Model
{
  /**
   * Type is not specified or unknown in this version.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_UNSPECIFIED = 'PRISMA_TYPE_UNSPECIFIED';
  /**
   * Display type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_DISPLAY = 'PRISMA_TYPE_DISPLAY';
  /**
   * Search type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_SEARCH = 'PRISMA_TYPE_SEARCH';
  /**
   * Video type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_VIDEO = 'PRISMA_TYPE_VIDEO';
  /**
   * Audio type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_AUDIO = 'PRISMA_TYPE_AUDIO';
  /**
   * Social type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_SOCIAL = 'PRISMA_TYPE_SOCIAL';
  /**
   * Fee type.
   */
  public const PRISMA_TYPE_PRISMA_TYPE_FEE = 'PRISMA_TYPE_FEE';
  protected $prismaCpeCodeType = PrismaCpeCode::class;
  protected $prismaCpeCodeDataType = '';
  /**
   * Required. The Prisma type.
   *
   * @var string
   */
  public $prismaType;
  /**
   * Required. The entity allocated this budget (DSP, site, etc.).
   *
   * @var string
   */
  public $supplier;

  /**
   * Required. Relevant client, product, and estimate codes from the Mediaocean
   * Prisma tool.
   *
   * @param PrismaCpeCode $prismaCpeCode
   */
  public function setPrismaCpeCode(PrismaCpeCode $prismaCpeCode)
  {
    $this->prismaCpeCode = $prismaCpeCode;
  }
  /**
   * @return PrismaCpeCode
   */
  public function getPrismaCpeCode()
  {
    return $this->prismaCpeCode;
  }
  /**
   * Required. The Prisma type.
   *
   * Accepted values: PRISMA_TYPE_UNSPECIFIED, PRISMA_TYPE_DISPLAY,
   * PRISMA_TYPE_SEARCH, PRISMA_TYPE_VIDEO, PRISMA_TYPE_AUDIO,
   * PRISMA_TYPE_SOCIAL, PRISMA_TYPE_FEE
   *
   * @param self::PRISMA_TYPE_* $prismaType
   */
  public function setPrismaType($prismaType)
  {
    $this->prismaType = $prismaType;
  }
  /**
   * @return self::PRISMA_TYPE_*
   */
  public function getPrismaType()
  {
    return $this->prismaType;
  }
  /**
   * Required. The entity allocated this budget (DSP, site, etc.).
   *
   * @param string $supplier
   */
  public function setSupplier($supplier)
  {
    $this->supplier = $supplier;
  }
  /**
   * @return string
   */
  public function getSupplier()
  {
    return $this->supplier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrismaConfig::class, 'Google_Service_DisplayVideo_PrismaConfig');
