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

namespace Google\Service\CloudAsset;

class SoftwarePackage extends \Google\Model
{
  protected $aptPackageType = VersionedPackage::class;
  protected $aptPackageDataType = '';
  protected $cosPackageType = VersionedPackage::class;
  protected $cosPackageDataType = '';
  protected $googetPackageType = VersionedPackage::class;
  protected $googetPackageDataType = '';
  protected $qfePackageType = WindowsQuickFixEngineeringPackage::class;
  protected $qfePackageDataType = '';
  protected $windowsApplicationType = WindowsApplication::class;
  protected $windowsApplicationDataType = '';
  protected $wuaPackageType = WindowsUpdatePackage::class;
  protected $wuaPackageDataType = '';
  protected $yumPackageType = VersionedPackage::class;
  protected $yumPackageDataType = '';
  protected $zypperPackageType = VersionedPackage::class;
  protected $zypperPackageDataType = '';
  protected $zypperPatchType = ZypperPatch::class;
  protected $zypperPatchDataType = '';

  /**
   * Details of an APT package. For details about the apt package manager, see
   * https://wiki.debian.org/Apt.
   *
   * @param VersionedPackage $aptPackage
   */
  public function setAptPackage(VersionedPackage $aptPackage)
  {
    $this->aptPackage = $aptPackage;
  }
  /**
   * @return VersionedPackage
   */
  public function getAptPackage()
  {
    return $this->aptPackage;
  }
  /**
   * Details of a COS package.
   *
   * @param VersionedPackage $cosPackage
   */
  public function setCosPackage(VersionedPackage $cosPackage)
  {
    $this->cosPackage = $cosPackage;
  }
  /**
   * @return VersionedPackage
   */
  public function getCosPackage()
  {
    return $this->cosPackage;
  }
  /**
   * Details of a Googet package. For details about the googet package manager,
   * see https://github.com/google/googet.
   *
   * @param VersionedPackage $googetPackage
   */
  public function setGoogetPackage(VersionedPackage $googetPackage)
  {
    $this->googetPackage = $googetPackage;
  }
  /**
   * @return VersionedPackage
   */
  public function getGoogetPackage()
  {
    return $this->googetPackage;
  }
  /**
   * Details of a Windows Quick Fix engineering package. See
   * https://docs.microsoft.com/en-
   * us/windows/win32/cimwin32prov/win32-quickfixengineering for info in Windows
   * Quick Fix Engineering.
   *
   * @param WindowsQuickFixEngineeringPackage $qfePackage
   */
  public function setQfePackage(WindowsQuickFixEngineeringPackage $qfePackage)
  {
    $this->qfePackage = $qfePackage;
  }
  /**
   * @return WindowsQuickFixEngineeringPackage
   */
  public function getQfePackage()
  {
    return $this->qfePackage;
  }
  /**
   * Details of Windows Application.
   *
   * @param WindowsApplication $windowsApplication
   */
  public function setWindowsApplication(WindowsApplication $windowsApplication)
  {
    $this->windowsApplication = $windowsApplication;
  }
  /**
   * @return WindowsApplication
   */
  public function getWindowsApplication()
  {
    return $this->windowsApplication;
  }
  /**
   * Details of a Windows Update package. See https://docs.microsoft.com/en-
   * us/windows/win32/api/_wua/ for information about Windows Update.
   *
   * @param WindowsUpdatePackage $wuaPackage
   */
  public function setWuaPackage(WindowsUpdatePackage $wuaPackage)
  {
    $this->wuaPackage = $wuaPackage;
  }
  /**
   * @return WindowsUpdatePackage
   */
  public function getWuaPackage()
  {
    return $this->wuaPackage;
  }
  /**
   * Yum package info. For details about the yum package manager, see
   * https://access.redhat.com/documentation/en-
   * us/red_hat_enterprise_linux/6/html/deployment_guide/ch-yum.
   *
   * @param VersionedPackage $yumPackage
   */
  public function setYumPackage(VersionedPackage $yumPackage)
  {
    $this->yumPackage = $yumPackage;
  }
  /**
   * @return VersionedPackage
   */
  public function getYumPackage()
  {
    return $this->yumPackage;
  }
  /**
   * Details of a Zypper package. For details about the Zypper package manager,
   * see https://en.opensuse.org/SDB:Zypper_manual.
   *
   * @param VersionedPackage $zypperPackage
   */
  public function setZypperPackage(VersionedPackage $zypperPackage)
  {
    $this->zypperPackage = $zypperPackage;
  }
  /**
   * @return VersionedPackage
   */
  public function getZypperPackage()
  {
    return $this->zypperPackage;
  }
  /**
   * Details of a Zypper patch. For details about the Zypper package manager,
   * see https://en.opensuse.org/SDB:Zypper_manual.
   *
   * @param ZypperPatch $zypperPatch
   */
  public function setZypperPatch(ZypperPatch $zypperPatch)
  {
    $this->zypperPatch = $zypperPatch;
  }
  /**
   * @return ZypperPatch
   */
  public function getZypperPatch()
  {
    return $this->zypperPatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SoftwarePackage::class, 'Google_Service_CloudAsset_SoftwarePackage');
