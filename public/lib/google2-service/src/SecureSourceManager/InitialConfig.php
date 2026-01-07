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

namespace Google\Service\SecureSourceManager;

class InitialConfig extends \Google\Collection
{
  protected $collection_key = 'gitignores';
  /**
   * Default branch name of the repository.
   *
   * @var string
   */
  public $defaultBranch;
  /**
   * List of gitignore template names user can choose from. Valid values:
   * actionscript, ada, agda, android, anjuta, ansible, appcelerator-titanium,
   * app-engine, archives, arch-linux-packages, atmel-studio, autotools, backup,
   * bazaar, bazel, bitrix, bricx-cc, c, cake-php, calabash, cf-wheels, chef-
   * cookbook, clojure, cloud9, c-make, code-igniter, code-kit, code-sniffer,
   * common-lisp, composer, concrete5, coq, cordova, cpp, craft-cms, cuda, cvs,
   * d, dart, dart-editor, delphi, diff, dm, dreamweaver, dropbox, drupal,
   * drupal-7, eagle, eclipse, eiffel-studio, elisp, elixir, elm, emacs, ensime,
   * epi-server, erlang, esp-idf, espresso, exercism, expression-engine, ext-js,
   * fancy, finale, flex-builder, force-dot-com, fortran, fuel-php, gcov, git-
   * book, gnome-shell-extension, go, godot, gpg, gradle, grails, gwt, haskell,
   * hugo, iar-ewarm, idris, igor-pro, images, infor-cms, java, jboss, jboss-4,
   * jboss-6, jdeveloper, jekyll, jenkins-home, jenv, jet-brains, jigsaw,
   * joomla, julia, jupyter-notebooks, kate, kdevelop4, kentico, ki-cad, kohana,
   * kotlin, lab-view, laravel, lazarus, leiningen, lemon-stand, libre-office,
   * lilypond, linux, lithium, logtalk, lua, lyx, mac-os, magento, magento-1,
   * magento-2, matlab, maven, mercurial, mercury, metals, meta-programming-
   * system, meteor, microsoft-office, model-sim, momentics, mono-develop,
   * nanoc, net-beans, nikola, nim, ninja, node, notepad-pp, nwjs, objective--c,
   * ocaml, octave, opa, open-cart, openssl, oracle-forms, otto, packer, patch,
   * perl, perl6, phalcon, phoenix, pimcore, play-framework, plone, prestashop,
   * processing, psoc-creator, puppet, pure-script, putty, python, qooxdoo, qt,
   * r, racket, rails, raku, red, redcar, redis, rhodes-rhomobile, ros, ruby,
   * rust, sam, sass, sbt, scala, scheme, scons, scrivener, sdcc, seam-gen,
   * sketch-up, slick-edit, smalltalk, snap, splunk, stata, stella, sublime-
   * text, sugar-crm, svn, swift, symfony, symphony-cms, synopsys-vcs, tags,
   * terraform, tex, text-mate, textpattern, think-php, tortoise-git, turbo-
   * gears-2, typo3, umbraco, unity, unreal-engine, vagrant, vim, virtual-env,
   * virtuoso, visual-studio, visual-studio-code, vue, vvvv, waf, web-methods,
   * windows, word-press, xcode, xilinx, xilinx-ise, xojo, yeoman, yii, zend-
   * framework, zephir.
   *
   * @var string[]
   */
  public $gitignores;
  /**
   * License template name user can choose from. Valid values: license-0bsd,
   * license-389-exception, aal, abstyles, adobe-2006, adobe-glyph, adsl,
   * afl-1-1, afl-1-2, afl-2-0, afl-2-1, afl-3-0, afmparse, agpl-1-0,
   * agpl-1-0-only, agpl-1-0-or-later, agpl-3-0-only, agpl-3-0-or-later,
   * aladdin, amdplpa, aml, ampas, antlr-pd, antlr-pd-fallback, apache-1-0,
   * apache-1-1, apache-2-0, apafml, apl-1-0, apsl-1-0, apsl-1-1, apsl-1-2,
   * apsl-2-0, artistic-1-0, artistic-1-0-cl8, artistic-1-0-perl, artistic-2-0,
   * autoconf-exception-2-0, autoconf-exception-3-0, bahyph, barr, beerware,
   * bison-exception-2-2, bittorrent-1-0, bittorrent-1-1, blessing,
   * blueoak-1-0-0, bootloader-exception, borceux, bsd-1-clause, bsd-2-clause,
   * bsd-2-clause-freebsd, bsd-2-clause-netbsd, bsd-2-clause-patent,
   * bsd-2-clause-views, bsd-3-clause, bsd-3-clause-attribution, bsd-3-clause-
   * clear, bsd-3-clause-lbnl, bsd-3-clause-modification, bsd-3-clause-no-
   * nuclear-license, bsd-3-clause-no-nuclear-license-2014, bsd-3-clause-no-
   * nuclear-warranty, bsd-3-clause-open-mpi, bsd-4-clause, bsd-4-clause-
   * shortened, bsd-4-clause-uc, bsd-protection, bsd-source-code, bsl-1-0,
   * busl-1-1, cal-1-0, cal-1-0-combined-work-exception, caldera, catosl-1-1,
   * cc0-1-0, cc-by-1-0, cc-by-2-0, cc-by-3-0, cc-by-3-0-at, cc-by-3-0-us, cc-
   * by-4-0, cc-by-nc-1-0, cc-by-nc-2-0, cc-by-nc-3-0, cc-by-nc-4-0, cc-by-nc-
   * nd-1-0, cc-by-nc-nd-2-0, cc-by-nc-nd-3-0, cc-by-nc-nd-3-0-igo, cc-by-nc-
   * nd-4-0, cc-by-nc-sa-1-0, cc-by-nc-sa-2-0, cc-by-nc-sa-3-0, cc-by-nc-sa-4-0,
   * cc-by-nd-1-0, cc-by-nd-2-0, cc-by-nd-3-0, cc-by-nd-4-0, cc-by-sa-1-0, cc-
   * by-sa-2-0, cc-by-sa-2-0-uk, cc-by-sa-2-1-jp, cc-by-sa-3-0, cc-by-sa-3-0-at,
   * cc-by-sa-4-0, cc-pddc, cddl-1-0, cddl-1-1, cdla-permissive-1-0, cdla-
   * sharing-1-0, cecill-1-0, cecill-1-1, cecill-2-0, cecill-2-1, cecill-b,
   * cecill-c, cern-ohl-1-1, cern-ohl-1-2, cern-ohl-p-2-0, cern-ohl-s-2-0, cern-
   * ohl-w-2-0, clartistic, classpath-exception-2-0, clisp-exception-2-0, cnri-
   * jython, cnri-python, cnri-python-gpl-compatible, condor-1-1, copyleft-
   * next-0-3-0, copyleft-next-0-3-1, cpal-1-0, cpl-1-0, cpol-1-02, crossword,
   * crystal-stacker, cua-opl-1-0, cube, c-uda-1-0, curl, d-fsl-1-0, diffmark,
   * digirule-foss-exception, doc, dotseqn, drl-1-0, dsdp, dvipdfm, ecl-1-0,
   * ecl-2-0, ecos-exception-2-0, efl-1-0, efl-2-0, egenix, entessa, epics,
   * epl-1-0, epl-2-0, erlpl-1-1, etalab-2-0, eu-datagrid, eupl-1-0, eupl-1-1,
   * eupl-1-2, eurosym, fair, fawkes-runtime-exception, fltk-exception, font-
   * exception-2-0, frameworx-1-0, freebsd-doc, freeimage, freertos-
   * exception-2-0, fsfap, fsful, fsfullr, ftl, gcc-exception-2-0, gcc-
   * exception-3-1, gd, gfdl-1-1-invariants-only, gfdl-1-1-invariants-or-later,
   * gfdl-1-1-no-invariants-only, gfdl-1-1-no-invariants-or-later,
   * gfdl-1-1-only, gfdl-1-1-or-later, gfdl-1-2-invariants-only,
   * gfdl-1-2-invariants-or-later, gfdl-1-2-no-invariants-only, gfdl-1-2-no-
   * invariants-or-later, gfdl-1-2-only, gfdl-1-2-or-later, gfdl-1-3-invariants-
   * only, gfdl-1-3-invariants-or-later, gfdl-1-3-no-invariants-only,
   * gfdl-1-3-no-invariants-or-later, gfdl-1-3-only, gfdl-1-3-or-later,
   * giftware, gl2ps, glide, glulxe, glwtpl, gnu-javamail-exception, gnuplot,
   * gpl-1-0-only, gpl-1-0-or-later, gpl-2-0-only, gpl-2-0-or-later,
   * gpl-3-0-linking-exception, gpl-3-0-linking-source-exception, gpl-3-0-only,
   * gpl-3-0-or-later, gpl-cc-1-0, gsoap-1-3b, haskell-report, hippocratic-2-1,
   * hpnd, hpnd-sell-variant, htmltidy, i2p-gpl-java-exception, ibm-pibs, icu,
   * ijg, image-magick, imatix, imlib2, info-zip, intel, intel-acpi,
   * interbase-1-0, ipa, ipl-1-0, isc, jasper-2-0, jpnic, json, lal-1-2,
   * lal-1-3, latex2e, leptonica, lgpl-2-0-only, lgpl-2-0-or-later,
   * lgpl-2-1-only, lgpl-2-1-or-later, lgpl-3-0-linking-exception,
   * lgpl-3-0-only, lgpl-3-0-or-later, lgpllr, libpng, libpng-2-0,
   * libselinux-1-0, libtiff, libtool-exception, liliq-p-1-1, liliq-r-1-1,
   * liliq-rplus-1-1, linux-openib, linux-syscall-note, llvm-exception, lpl-1-0,
   * lpl-1-02, lppl-1-0, lppl-1-1, lppl-1-2, lppl-1-3a, lppl-1-3c, lzma-
   * exception, make-index, mif-exception, miros, mit, mit-0, mit-advertising,
   * mit-cmu, mit-enna, mit-feh, mit-modern-variant, mitnfa, mit-open-group,
   * motosoto, mpich2, mpl-1-0, mpl-1-1, mpl-2-0, mpl-2-0-no-copyleft-exception,
   * ms-pl, ms-rl, mtll, mulanpsl-1-0, mulanpsl-2-0, multics, mup, naist-2003,
   * nasa-1-3, naumen, nbpl-1-0, ncgl-uk-2-0, ncsa, netcdf, net-snmp, newsletr,
   * ngpl, nist-pd, nist-pd-fallback, nlod-1-0, nlpl, nokia, nokia-qt-
   * exception-1-1, nosl, noweb, npl-1-0, npl-1-1, nposl-3-0, nrl, ntp, ntp-0,
   * ocaml-lgpl-linking-exception, occt-exception-1-0, occt-pl, oclc-2-0,
   * odbl-1-0, odc-by-1-0, ofl-1-0, ofl-1-0-no-rfn, ofl-1-0-rfn, ofl-1-1,
   * ofl-1-1-no-rfn, ofl-1-1-rfn, ogc-1-0, ogdl-taiwan-1-0, ogl-canada-2-0, ogl-
   * uk-1-0, ogl-uk-2-0, ogl-uk-3-0, ogtsl, oldap-1-1, oldap-1-2, oldap-1-3,
   * oldap-1-4, oldap-2-0, oldap-2-0-1, oldap-2-1, oldap-2-2, oldap-2-2-1,
   * oldap-2-2-2, oldap-2-3, oldap-2-4, oldap-2-7, oml, openjdk-assembly-
   * exception-1-0, openssl, openvpn-openssl-exception, opl-1-0, oset-pl-2-1,
   * osl-1-0, osl-1-1, osl-2-0, osl-2-1, osl-3-0, o-uda-1-0, parity-6-0-0,
   * parity-7-0-0, pddl-1-0, php-3-0, php-3-01, plexus, polyform-
   * noncommercial-1-0-0, polyform-small-business-1-0-0, postgresql, psf-2-0,
   * psfrag, ps-or-pdf-font-exception-20170817, psutils, python-2-0, qhull,
   * qpl-1-0, qt-gpl-exception-1-0, qt-lgpl-exception-1-1, qwt-exception-1-0,
   * rdisc, rhecos-1-1, rpl-1-1, rpsl-1-0, rsa-md, rscpl, ruby, saxpath, sax-pd,
   * scea, sendmail, sendmail-8-23, sgi-b-1-0, sgi-b-1-1, sgi-b-2-0, shl-0-51,
   * shl-2-0, shl-2-1, simpl-2-0, sissl, sissl-1-2, sleepycat, smlnj, smppl,
   * snia, spencer-86, spencer-94, spencer-99, spl-1-0, ssh-openssh, ssh-short,
   * sspl-1-0, sugarcrm-1-1-3, swift-exception, swl, tapr-ohl-1-0, tcl, tcp-
   * wrappers, tmate, torque-1-1, tosl, tu-berlin-1-0, tu-berlin-2-0, u-boot-
   * exception-2-0, ucl-1-0, unicode-dfs-2015, unicode-dfs-2016, unicode-tou,
   * universal-foss-exception-1-0, unlicense, upl-1-0, vim, vostrom, vsl-1-0,
   * w3c, w3c-19980720, w3c-20150513, watcom-1-0, wsuipa, wtfpl, wxwindows-
   * exception-3-1, x11, xerox, xfree86-1-1, xinetd, xnet, xpp, xskat, ypl-1-0,
   * ypl-1-1, zed, zend-2-0, zimbra-1-3, zimbra-1-4, zlib, zlib-acknowledgement,
   * zpl-1-1, zpl-2-0, zpl-2-1.
   *
   * @var string
   */
  public $license;
  /**
   * README template name. Valid template name(s) are: default.
   *
   * @var string
   */
  public $readme;

  /**
   * Default branch name of the repository.
   *
   * @param string $defaultBranch
   */
  public function setDefaultBranch($defaultBranch)
  {
    $this->defaultBranch = $defaultBranch;
  }
  /**
   * @return string
   */
  public function getDefaultBranch()
  {
    return $this->defaultBranch;
  }
  /**
   * List of gitignore template names user can choose from. Valid values:
   * actionscript, ada, agda, android, anjuta, ansible, appcelerator-titanium,
   * app-engine, archives, arch-linux-packages, atmel-studio, autotools, backup,
   * bazaar, bazel, bitrix, bricx-cc, c, cake-php, calabash, cf-wheels, chef-
   * cookbook, clojure, cloud9, c-make, code-igniter, code-kit, code-sniffer,
   * common-lisp, composer, concrete5, coq, cordova, cpp, craft-cms, cuda, cvs,
   * d, dart, dart-editor, delphi, diff, dm, dreamweaver, dropbox, drupal,
   * drupal-7, eagle, eclipse, eiffel-studio, elisp, elixir, elm, emacs, ensime,
   * epi-server, erlang, esp-idf, espresso, exercism, expression-engine, ext-js,
   * fancy, finale, flex-builder, force-dot-com, fortran, fuel-php, gcov, git-
   * book, gnome-shell-extension, go, godot, gpg, gradle, grails, gwt, haskell,
   * hugo, iar-ewarm, idris, igor-pro, images, infor-cms, java, jboss, jboss-4,
   * jboss-6, jdeveloper, jekyll, jenkins-home, jenv, jet-brains, jigsaw,
   * joomla, julia, jupyter-notebooks, kate, kdevelop4, kentico, ki-cad, kohana,
   * kotlin, lab-view, laravel, lazarus, leiningen, lemon-stand, libre-office,
   * lilypond, linux, lithium, logtalk, lua, lyx, mac-os, magento, magento-1,
   * magento-2, matlab, maven, mercurial, mercury, metals, meta-programming-
   * system, meteor, microsoft-office, model-sim, momentics, mono-develop,
   * nanoc, net-beans, nikola, nim, ninja, node, notepad-pp, nwjs, objective--c,
   * ocaml, octave, opa, open-cart, openssl, oracle-forms, otto, packer, patch,
   * perl, perl6, phalcon, phoenix, pimcore, play-framework, plone, prestashop,
   * processing, psoc-creator, puppet, pure-script, putty, python, qooxdoo, qt,
   * r, racket, rails, raku, red, redcar, redis, rhodes-rhomobile, ros, ruby,
   * rust, sam, sass, sbt, scala, scheme, scons, scrivener, sdcc, seam-gen,
   * sketch-up, slick-edit, smalltalk, snap, splunk, stata, stella, sublime-
   * text, sugar-crm, svn, swift, symfony, symphony-cms, synopsys-vcs, tags,
   * terraform, tex, text-mate, textpattern, think-php, tortoise-git, turbo-
   * gears-2, typo3, umbraco, unity, unreal-engine, vagrant, vim, virtual-env,
   * virtuoso, visual-studio, visual-studio-code, vue, vvvv, waf, web-methods,
   * windows, word-press, xcode, xilinx, xilinx-ise, xojo, yeoman, yii, zend-
   * framework, zephir.
   *
   * @param string[] $gitignores
   */
  public function setGitignores($gitignores)
  {
    $this->gitignores = $gitignores;
  }
  /**
   * @return string[]
   */
  public function getGitignores()
  {
    return $this->gitignores;
  }
  /**
   * License template name user can choose from. Valid values: license-0bsd,
   * license-389-exception, aal, abstyles, adobe-2006, adobe-glyph, adsl,
   * afl-1-1, afl-1-2, afl-2-0, afl-2-1, afl-3-0, afmparse, agpl-1-0,
   * agpl-1-0-only, agpl-1-0-or-later, agpl-3-0-only, agpl-3-0-or-later,
   * aladdin, amdplpa, aml, ampas, antlr-pd, antlr-pd-fallback, apache-1-0,
   * apache-1-1, apache-2-0, apafml, apl-1-0, apsl-1-0, apsl-1-1, apsl-1-2,
   * apsl-2-0, artistic-1-0, artistic-1-0-cl8, artistic-1-0-perl, artistic-2-0,
   * autoconf-exception-2-0, autoconf-exception-3-0, bahyph, barr, beerware,
   * bison-exception-2-2, bittorrent-1-0, bittorrent-1-1, blessing,
   * blueoak-1-0-0, bootloader-exception, borceux, bsd-1-clause, bsd-2-clause,
   * bsd-2-clause-freebsd, bsd-2-clause-netbsd, bsd-2-clause-patent,
   * bsd-2-clause-views, bsd-3-clause, bsd-3-clause-attribution, bsd-3-clause-
   * clear, bsd-3-clause-lbnl, bsd-3-clause-modification, bsd-3-clause-no-
   * nuclear-license, bsd-3-clause-no-nuclear-license-2014, bsd-3-clause-no-
   * nuclear-warranty, bsd-3-clause-open-mpi, bsd-4-clause, bsd-4-clause-
   * shortened, bsd-4-clause-uc, bsd-protection, bsd-source-code, bsl-1-0,
   * busl-1-1, cal-1-0, cal-1-0-combined-work-exception, caldera, catosl-1-1,
   * cc0-1-0, cc-by-1-0, cc-by-2-0, cc-by-3-0, cc-by-3-0-at, cc-by-3-0-us, cc-
   * by-4-0, cc-by-nc-1-0, cc-by-nc-2-0, cc-by-nc-3-0, cc-by-nc-4-0, cc-by-nc-
   * nd-1-0, cc-by-nc-nd-2-0, cc-by-nc-nd-3-0, cc-by-nc-nd-3-0-igo, cc-by-nc-
   * nd-4-0, cc-by-nc-sa-1-0, cc-by-nc-sa-2-0, cc-by-nc-sa-3-0, cc-by-nc-sa-4-0,
   * cc-by-nd-1-0, cc-by-nd-2-0, cc-by-nd-3-0, cc-by-nd-4-0, cc-by-sa-1-0, cc-
   * by-sa-2-0, cc-by-sa-2-0-uk, cc-by-sa-2-1-jp, cc-by-sa-3-0, cc-by-sa-3-0-at,
   * cc-by-sa-4-0, cc-pddc, cddl-1-0, cddl-1-1, cdla-permissive-1-0, cdla-
   * sharing-1-0, cecill-1-0, cecill-1-1, cecill-2-0, cecill-2-1, cecill-b,
   * cecill-c, cern-ohl-1-1, cern-ohl-1-2, cern-ohl-p-2-0, cern-ohl-s-2-0, cern-
   * ohl-w-2-0, clartistic, classpath-exception-2-0, clisp-exception-2-0, cnri-
   * jython, cnri-python, cnri-python-gpl-compatible, condor-1-1, copyleft-
   * next-0-3-0, copyleft-next-0-3-1, cpal-1-0, cpl-1-0, cpol-1-02, crossword,
   * crystal-stacker, cua-opl-1-0, cube, c-uda-1-0, curl, d-fsl-1-0, diffmark,
   * digirule-foss-exception, doc, dotseqn, drl-1-0, dsdp, dvipdfm, ecl-1-0,
   * ecl-2-0, ecos-exception-2-0, efl-1-0, efl-2-0, egenix, entessa, epics,
   * epl-1-0, epl-2-0, erlpl-1-1, etalab-2-0, eu-datagrid, eupl-1-0, eupl-1-1,
   * eupl-1-2, eurosym, fair, fawkes-runtime-exception, fltk-exception, font-
   * exception-2-0, frameworx-1-0, freebsd-doc, freeimage, freertos-
   * exception-2-0, fsfap, fsful, fsfullr, ftl, gcc-exception-2-0, gcc-
   * exception-3-1, gd, gfdl-1-1-invariants-only, gfdl-1-1-invariants-or-later,
   * gfdl-1-1-no-invariants-only, gfdl-1-1-no-invariants-or-later,
   * gfdl-1-1-only, gfdl-1-1-or-later, gfdl-1-2-invariants-only,
   * gfdl-1-2-invariants-or-later, gfdl-1-2-no-invariants-only, gfdl-1-2-no-
   * invariants-or-later, gfdl-1-2-only, gfdl-1-2-or-later, gfdl-1-3-invariants-
   * only, gfdl-1-3-invariants-or-later, gfdl-1-3-no-invariants-only,
   * gfdl-1-3-no-invariants-or-later, gfdl-1-3-only, gfdl-1-3-or-later,
   * giftware, gl2ps, glide, glulxe, glwtpl, gnu-javamail-exception, gnuplot,
   * gpl-1-0-only, gpl-1-0-or-later, gpl-2-0-only, gpl-2-0-or-later,
   * gpl-3-0-linking-exception, gpl-3-0-linking-source-exception, gpl-3-0-only,
   * gpl-3-0-or-later, gpl-cc-1-0, gsoap-1-3b, haskell-report, hippocratic-2-1,
   * hpnd, hpnd-sell-variant, htmltidy, i2p-gpl-java-exception, ibm-pibs, icu,
   * ijg, image-magick, imatix, imlib2, info-zip, intel, intel-acpi,
   * interbase-1-0, ipa, ipl-1-0, isc, jasper-2-0, jpnic, json, lal-1-2,
   * lal-1-3, latex2e, leptonica, lgpl-2-0-only, lgpl-2-0-or-later,
   * lgpl-2-1-only, lgpl-2-1-or-later, lgpl-3-0-linking-exception,
   * lgpl-3-0-only, lgpl-3-0-or-later, lgpllr, libpng, libpng-2-0,
   * libselinux-1-0, libtiff, libtool-exception, liliq-p-1-1, liliq-r-1-1,
   * liliq-rplus-1-1, linux-openib, linux-syscall-note, llvm-exception, lpl-1-0,
   * lpl-1-02, lppl-1-0, lppl-1-1, lppl-1-2, lppl-1-3a, lppl-1-3c, lzma-
   * exception, make-index, mif-exception, miros, mit, mit-0, mit-advertising,
   * mit-cmu, mit-enna, mit-feh, mit-modern-variant, mitnfa, mit-open-group,
   * motosoto, mpich2, mpl-1-0, mpl-1-1, mpl-2-0, mpl-2-0-no-copyleft-exception,
   * ms-pl, ms-rl, mtll, mulanpsl-1-0, mulanpsl-2-0, multics, mup, naist-2003,
   * nasa-1-3, naumen, nbpl-1-0, ncgl-uk-2-0, ncsa, netcdf, net-snmp, newsletr,
   * ngpl, nist-pd, nist-pd-fallback, nlod-1-0, nlpl, nokia, nokia-qt-
   * exception-1-1, nosl, noweb, npl-1-0, npl-1-1, nposl-3-0, nrl, ntp, ntp-0,
   * ocaml-lgpl-linking-exception, occt-exception-1-0, occt-pl, oclc-2-0,
   * odbl-1-0, odc-by-1-0, ofl-1-0, ofl-1-0-no-rfn, ofl-1-0-rfn, ofl-1-1,
   * ofl-1-1-no-rfn, ofl-1-1-rfn, ogc-1-0, ogdl-taiwan-1-0, ogl-canada-2-0, ogl-
   * uk-1-0, ogl-uk-2-0, ogl-uk-3-0, ogtsl, oldap-1-1, oldap-1-2, oldap-1-3,
   * oldap-1-4, oldap-2-0, oldap-2-0-1, oldap-2-1, oldap-2-2, oldap-2-2-1,
   * oldap-2-2-2, oldap-2-3, oldap-2-4, oldap-2-7, oml, openjdk-assembly-
   * exception-1-0, openssl, openvpn-openssl-exception, opl-1-0, oset-pl-2-1,
   * osl-1-0, osl-1-1, osl-2-0, osl-2-1, osl-3-0, o-uda-1-0, parity-6-0-0,
   * parity-7-0-0, pddl-1-0, php-3-0, php-3-01, plexus, polyform-
   * noncommercial-1-0-0, polyform-small-business-1-0-0, postgresql, psf-2-0,
   * psfrag, ps-or-pdf-font-exception-20170817, psutils, python-2-0, qhull,
   * qpl-1-0, qt-gpl-exception-1-0, qt-lgpl-exception-1-1, qwt-exception-1-0,
   * rdisc, rhecos-1-1, rpl-1-1, rpsl-1-0, rsa-md, rscpl, ruby, saxpath, sax-pd,
   * scea, sendmail, sendmail-8-23, sgi-b-1-0, sgi-b-1-1, sgi-b-2-0, shl-0-51,
   * shl-2-0, shl-2-1, simpl-2-0, sissl, sissl-1-2, sleepycat, smlnj, smppl,
   * snia, spencer-86, spencer-94, spencer-99, spl-1-0, ssh-openssh, ssh-short,
   * sspl-1-0, sugarcrm-1-1-3, swift-exception, swl, tapr-ohl-1-0, tcl, tcp-
   * wrappers, tmate, torque-1-1, tosl, tu-berlin-1-0, tu-berlin-2-0, u-boot-
   * exception-2-0, ucl-1-0, unicode-dfs-2015, unicode-dfs-2016, unicode-tou,
   * universal-foss-exception-1-0, unlicense, upl-1-0, vim, vostrom, vsl-1-0,
   * w3c, w3c-19980720, w3c-20150513, watcom-1-0, wsuipa, wtfpl, wxwindows-
   * exception-3-1, x11, xerox, xfree86-1-1, xinetd, xnet, xpp, xskat, ypl-1-0,
   * ypl-1-1, zed, zend-2-0, zimbra-1-3, zimbra-1-4, zlib, zlib-acknowledgement,
   * zpl-1-1, zpl-2-0, zpl-2-1.
   *
   * @param string $license
   */
  public function setLicense($license)
  {
    $this->license = $license;
  }
  /**
   * @return string
   */
  public function getLicense()
  {
    return $this->license;
  }
  /**
   * README template name. Valid template name(s) are: default.
   *
   * @param string $readme
   */
  public function setReadme($readme)
  {
    $this->readme = $readme;
  }
  /**
   * @return string
   */
  public function getReadme()
  {
    return $this->readme;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InitialConfig::class, 'Google_Service_SecureSourceManager_InitialConfig');
