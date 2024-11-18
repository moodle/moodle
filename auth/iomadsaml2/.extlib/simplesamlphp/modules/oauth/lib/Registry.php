<?php

namespace SimpleSAML\Module\oauth;

/**
 * Editor for OAuth Client Registry
 *
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */
class Registry
{
    /**
     * @param array $entry
     * @param string $userid
     * @return void
     * @throws \Exception
     */
    public static function requireOwnership($entry, $userid)
    {
        if (!isset($entry['owner'])) {
            throw new \Exception('OAuth Consumer has no owner. Which means no one is granted access, not even you.');
        } elseif ($entry['owner'] !== $userid) {
            throw new \Exception(
                'OAuth Consumer has an owner that is not equal to your userid, hence you are not granted access.'
            );
        }
    }


    /**
     * @param array $request
     * @param array &$entry
     * @param string $key
     * @return void
     */
    protected function getStandardField($request, &$entry, $key)
    {
        if (array_key_exists('field_'.$key, $request)) {
            $entry[$key] = $request['field_'.$key];
        } elseif (isset($entry[$key])) {
            unset($entry[$key]);
        }
    }


    /**
     * @param array $request
     * @param array $entry
     * @param array|null $override
     * @return array
     */
    public function formToMeta($request, $entry = [], $override = null)
    {
        $this->getStandardField($request, $entry, 'name');
        $this->getStandardField($request, $entry, 'description');
        $this->getStandardField($request, $entry, 'key');
        $this->getStandardField($request, $entry, 'secret');
        $this->getStandardField($request, $entry, 'RSAcertificate');
        $this->getStandardField($request, $entry, 'callback_url');

        if ($override) {
            foreach ($override as $key => $value) {
                $entry[$key] = $value;
            }
        }
        return $entry;
    }


    /**
     * @param array $request
     * @param string $key
     * @return void
     * @throws \Exception
     */
    protected function requireStandardField($request, $key)
    {
        if (!array_key_exists('field_'.$key, $request)) {
            throw new \Exception('Required field ['.$key.'] was missing.');
        }
        if (empty($request['field_'.$key])) {
            throw new \Exception('Required field ['.$key.'] was empty.');
        }
    }


    /**
     * @param array $request
     * @return void
     */
    public function checkForm($request)
    {
        $this->requireStandardField($request, 'name');
        $this->requireStandardField($request, 'description');
        $this->requireStandardField($request, 'key');
    }


    /**
     * @param string $name
     * @return string
     */
    protected function header($name)
    {
        return '<tr><td>&nbsp;</td><td class="header">'.$name.'</td></tr>';
    }


    /**
     * @param array $metadata
     * @param string $key
     * @param string $name
     * @return string
     */
    protected function readonlyDateField($metadata, $key, $name)
    {
        $value = '<span style="color: #aaa">Not set</a>';
        if (array_key_exists($key, $metadata)) {
            $value = date('j. F Y, G:i', $metadata[$key]);
        }
        return '<tr><td class="name">'.$name.'</td><td class="data">'.$value.'</td></tr>';
    }


    /**
     * @param array $metadata
     * @param string $key
     * @param string $name
     * @return string
     */
    protected function readonlyField($metadata, $key, $name)
    {
        $value = '';
        if (array_key_exists($key, $metadata)) {
            $value = $metadata[$key];
        }
        return '<tr><td class="name">'.$name.'</td><td class="data">'.htmlspecialchars($value).'</td></tr>';
    }


    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function hiddenField($key, $value)
    {
        return '<input type="hidden" name="'.$key.'" value="'.htmlspecialchars($value).'" />';
    }


    /**
     * @param array &$metadata
     * @param string $key
     * @return void
     */
    protected function flattenLanguageField(&$metadata, $key)
    {
        if (array_key_exists($key, $metadata)) {
            if (is_array($metadata[$key])) {
                if (isset($metadata[$key]['en'])) {
                    $metadata[$key] = $metadata[$key]['en'];
                } else {
                    unset($metadata[$key]);
                }
            }
        }
    }


    /**
     * @param array $metadata
     * @param string $key
     * @param string $name
     * @param bool $textarea
     * @return string
     */
    protected function standardField($metadata, $key, $name, $textarea = false)
    {
        $value = '';
        if (array_key_exists($key, $metadata)) {
            $value = htmlspecialchars($metadata[$key]);
        }

        if ($textarea) {
            return '<tr><td class="name">'.$name.'</td><td class="data">
                <textarea name="field_'.$key.'" rows="5" cols="50">'.$value.'</textarea></td></tr>';
        } else {
            return '<tr><td class="name">'.$name.'</td><td class="data">
                <input type="text" size="60" name="field_'.$key.'" value="'.$value.'" /></td></tr>';
        }
    }


    /**
     * @param array $metadata
     * @return string
     */
    public function metaToForm($metadata)
    {
        return '<form action="registry.edit.php" method="post">'.
            '<div id="tabdiv">'.
            '<ul class="tabset_tabs">'.
            '<li class="tab-link current" data-tab="basic"><a href="#basic">Name and description</a></li>'.
            '</ul>'.
            '<div id="basic" class="tabset_content current"><table class="formtable">'.
                $this->standardField($metadata, 'name', 'Name of client').
                $this->standardField($metadata, 'description', 'Description of client', true).
                $this->readonlyField($metadata, 'owner', 'Owner').
                $this->standardField($metadata, 'key', 'Consumer Key').
                $this->readonlyField($metadata, 'secret', 'Consumer Secret<br />(Used for HMAC_SHA1 signatures)').
                $this->standardField(
                    $metadata,
                    'RSAcertificate',
                    'RSA certificate (PEM)<br />(Used for RSA_SHA1 signatures)',
                    true
                ).
                $this->standardField($metadata, 'callback_url', 'Static/enforcing callback-url').
            '</table></div>'.
            '</div>'.
            $this->hiddenField('field_secret', $metadata['secret']).
            '<input type="submit" name="submit" value="Save" style="margin-top: 5px" />'.
            '</form>';
    }
}
