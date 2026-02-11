<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 *
 * @date    2017-01-03
 *
 * @license LGPLv3
 *
 * @url     <https://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2017 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 */

namespace Smalot\PdfParser;

use Smalot\PdfParser\Element\ElementArray;

/**
 * Class Pages
 */
class Pages extends PDFObject
{
    /**
     * @var array<\Smalot\PdfParser\Font>|null
     */
    protected $fonts;

    /**
     * @todo Objects other than Pages or Page might need to be treated specifically
     *       in order to get Page objects out of them.
     *
     * @see https://github.com/smalot/pdfparser/issues/331
     */
    public function getPages(bool $deep = false): array
    {
        if (!$this->has('Kids')) {
            return [];
        }

        /** @var ElementArray $kidsElement */
        $kidsElement = $this->get('Kids');

        if (!$deep) {
            return $kidsElement->getContent();
        }

        // Prepare to apply the Pages' object's fonts to each page
        if (false === \is_array($this->fonts)) {
            $this->setupFonts();
        }
        $fontsAvailable = 0 < \count($this->fonts);

        $kids = $kidsElement->getContent();
        $pages = [];

        foreach ($kids as $kid) {
            if ($kid instanceof self) {
                $pages = array_merge($pages, $kid->getPages(true));
            } elseif ($kid instanceof Page) {
                if ($fontsAvailable) {
                    $kid->setFonts($this->fonts);
                }
                $pages[] = $kid;
            }
        }

        return $pages;
    }

    /**
     * Gathers information about fonts and collects them in a list.
     *
     * @return void
     *
     * @internal
     */
    protected function setupFonts()
    {
        $resources = $this->get('Resources');

        if (method_exists($resources, 'has') && $resources->has('Font')) {
            // no fonts available, therefore stop here
            if ($resources->get('Font') instanceof Element\ElementMissing) {
                return;
            }

            if ($resources->get('Font') instanceof Header) {
                $fonts = $resources->get('Font')->getElements();
            } else {
                $fonts = $resources->get('Font')->getHeader()->getElements();
            }

            $table = [];

            foreach ($fonts as $id => $font) {
                if ($font instanceof Font) {
                    $table[$id] = $font;

                    // Store too on cleaned id value (only numeric)
                    $id = preg_replace('/[^0-9\.\-_]/', '', $id);
                    if ('' != $id) {
                        $table[$id] = $font;
                    }
                }
            }

            $this->fonts = $table;
        } else {
            $this->fonts = [];
        }
    }
}
