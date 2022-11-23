<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tiny equation plugin settings.
 *
 * @package    tiny_equation
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('editortiny', new admin_category('tiny_equation', new lang_string('pluginname', 'tiny_equation')));
$settings = new admin_settingpage('tiny_equation_settings', new lang_string('settings', 'tiny_equation'));

if ($ADMIN->fulltree) {
    // Group 1.
    $name = new lang_string('librarygroup1', 'tiny_equation');
    $desc = new lang_string('librarygroup1_desc', 'tiny_equation');
    $default = '
\cdot
\times
\ast
\div
\diamond
\pm
\mp
\oplus
\ominus
\otimes
\oslash
\odot
\circ
\bullet
\asymp
\equiv
\subseteq
\supseteq
\leq
\geq
\preceq
\succeq
\sim
\simeq
\approx
\subset
\supset
\ll
\gg
\prec
\succ
\infty
\in
\ni
\forall
\exists
\neq
';
    $setting = new admin_setting_configtextarea('tiny_equation/librarygroup1',
        $name,
        $desc,
        $default);
    $settings->add($setting);

    // Group 2.
    $name = new lang_string('librarygroup2', 'tiny_equation');
    $desc = new lang_string('librarygroup2_desc', 'tiny_equation');
    $default = '
\leftarrow
\rightarrow
\uparrow
\downarrow
\leftrightarrow
\nearrow
\searrow
\swarrow
\nwarrow
\Leftarrow
\Rightarrow
\Uparrow
\Downarrow
\Leftrightarrow
';
    $setting = new admin_setting_configtextarea('tiny_equation/librarygroup2',
        $name,
        $desc,
        $default);
    $settings->add($setting);

    // Group 3.
    $name = new lang_string('librarygroup3', 'tiny_equation');
    $desc = new lang_string('librarygroup3_desc', 'tiny_equation');
    $default = '
\alpha
\beta
\gamma
\delta
\epsilon
\zeta
\eta
\theta
\iota
\kappa
\lambda
\mu
\nu
\xi
\pi
\rho
\sigma
\tau
\upsilon
\phi
\chi
\psi
\omega
\Gamma
\Delta
\Theta
\Lambda
\Xi
\Pi
\Sigma
\Upsilon
\Phi
\Psi
\Omega
';
    $setting = new admin_setting_configtextarea('tiny_equation/librarygroup3',
        $name,
        $desc,
        $default);
    $settings->add($setting);

    // Group 4.
    $name = new lang_string('librarygroup4', 'tiny_equation');
    $desc = new lang_string('librarygroup4_desc', 'tiny_equation');
    $default = '
\sum{a,b}
\sqrt[a]{b+c}
\int_{a}^{b}{c}
\iint_{a}^{b}{c}
\iiint_{a}^{b}{c}
\oint{a}
(a)
[a]
\lbrace{a}\rbrace
\left| \begin{matrix} a_1 & a_2 \\\\ a_3 & a_4 \end{matrix} \right|
\frac{a}{b+c}
\vec{a}
\binom {a} {b}
{a \brack b}
{a \brace b}
';
    $setting = new admin_setting_configtextarea('tiny_equation/librarygroup4',
        $name,
        $desc,
        $default);
    $settings->add($setting);
}
