<?php

namespace Gettext\Languages\Exporter;

class Html extends Exporter
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build a HTML table';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::toStringDo()
     */
    protected static function toStringDo($languages)
    {
        return self::buildTable($languages, false);
    }

    protected static function h($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
    }

    protected static function buildTable($languages, $forDocs)
    {
        $prefix = $forDocs ? '            ' : '';
        $lines = array();
        $lines[] = $prefix . '<table' . ($forDocs ? ' class="table table-bordered table-condensed table-striped"' : '') . '>';
        $lines[] = $prefix . '    <thead>';
        $lines[] = $prefix . '        <tr>';
        $lines[] = $prefix . '            <th>Language code</th>';
        $lines[] = $prefix . '            <th>Language name</th>';
        $lines[] = $prefix . '            <th># plurals</th>';
        $lines[] = $prefix . '            <th>Formula</th>';
        $lines[] = $prefix . '            <th>Plurals</th>';
        $lines[] = $prefix . '        </tr>';
        $lines[] = $prefix . '    </thead>';
        $lines[] = $prefix . '    <tbody>';
        foreach ($languages as $lc) {
            $lines[] = $prefix . '        <tr>';
            $lines[] = $prefix . '            <td>' . $lc->id . '</td>';
            $name = self::h($lc->name);
            if (isset($lc->supersededBy)) {
                $name .= '<br /><small><span>Superseded by</span> ' . $lc->supersededBy . '</small>';
            }
            $lines[] = $prefix . '            <td>' . $name . '</td>';
            $lines[] = $prefix . '            <td>' . count($lc->categories) . '</td>';
            $lines[] = $prefix . '            <td>' . self::h($lc->formula) . '</td>';
            $cases = array();
            foreach ($lc->categories as $c) {
                $cases[] = '<li><span>' . $c->id . '</span><code>' . self::h($c->examples) . '</code></li>';
            }
            $lines[] = $prefix . '            <td><ol' . ($forDocs ? ' class="cases"' : '') . ' start="0">' . implode('', $cases) . '</ol></td>';
            $lines[] = $prefix . '        </tr>';
        }
        $lines[] = $prefix . '    </tbody>';
        $lines[] = $prefix . '</table>';

        return implode("\n", $lines);
    }
}
