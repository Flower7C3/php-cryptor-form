<?php
/**
 * Created by PhpStorm.
 * User: bkwiatek
 * Date: 2019-02-11
 * Time: 23:14
 */

namespace App;

use Symfony\Component\HttpFoundation\Request;

class CryptorApp
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function getSecretHash()
    {
        return strtoupper(substr(md5($this->secret), 0, 7));
    }

    public function encryptData(Request $request)
    {
        $success = false;
        $data = [];
        $errors = [];

        if (empty($this->secret)) {
            $errors['form'][] = 'Instance secret key is empty. Please fix app configuration!';
        }

        $randomSecret = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
        $data['secret'] = $request->request->get('secret', $randomSecret);
        $data['secret2'] = $request->request->get('secret2', $randomSecret);
        $data['encrypted'] = '';
        $data['decrypted'] = $request->request->get('decrypted');

        if ($request->getMethod() === 'POST') {
            if (empty($data['secret'])) {
                $errors['secret'][] = 'Field can not be empty!';
            }
            if (empty($data['secret2'])) {
                $errors['secret2'][] = 'Field can not be empty!';
            }
            if ($data['secret'] !== $data['secret2']) {
                $errors['secret'][] = 'The given secrets are not the same!';
            }
            if (empty($data['decrypted'])) {
                $errors['decrypted'][] = 'Field can not be empty!';
            } else {
                $decryptedClean = strip_tags($data['decrypted']);
                if ($data['decrypted'] !== $decryptedClean) {
                    $errors['decrypted'][] = 'Invalid characters found!';
                }
            }
            if (empty($errors)) {
                $cryptor = new Cryptor($this->secret . $data['secret']);
                $data['encrypted'] = $cryptor->encrypt($data['decrypted']);
                if (!empty($data['encrypted'])) {
                    $success = true;
                } else {
                    $errors['form'][] = 'Invalid enrypt response!';
                }

            }
        }

        return [
            'success' => $success,
            'data' => $this->formatData($data),
            'errors' => $errors,
        ];

    }

    public function decryptData(Request $request)
    {
        $success = false;
        $data = [];
        $errors = [];

        if (empty($this->secret)) {
            $errors['form'][] = 'Instance secret key is empty. Please fix app configuration!';
        }

        $data['secret'] = $request->request->get('secret');
        $data['encrypted'] = $request->attributes->get('encrypted', $request->query->get('encrypted', $request->request->get('encrypted')));
        $data['decrypted'] = '';

        if ($request->attributes->has('hash') && $request->attributes->get('hash') !== $this->getSecretHash()) {
            $errors['hash'][] = sprintf('Given request hash <code>%s</code> is different than this instance hash.', $request->get('hash'));
        }

        if ($request->getMethod() === 'POST') {
            if (empty($data['secret'])) {
                $errors['secret'][] = 'Field can not be empty!';
            }
            if (empty($data['encrypted'])) {
                $errors['encrypted'][] = 'Field can not be empty!';
            }
            if (empty($errors)) {
                $cryptor = new Cryptor($this->secret . $data['secret']);
                $data['decrypted'] = $cryptor->decrypt($data['encrypted']);
                if (!empty($data['decrypted'])) {
                    $success = true;
                } else {
                    $errors['form'][] = 'Invalid decrypt response!';
                }
            }
        }

        return [
            'success' => $success,
            'data' => $this->formatData($data),
            'errors' => $errors,
        ];
    }

    private function formatData($data)
    {
        $data['decryptedNice'] = $data['decrypted'];
        if (preg_match("'\n'", $data['decryptedNice'])) {
            $rows = explode("\n", $data['decryptedNice']);
            foreach ($rows as $index => $row) {
                $row = trim($row);
                $row = preg_replace("'^([A-Za-z0-9-_ ]+): (https?:\/\/[\w\-\.!~#?&=+\*\'\"(),\/]+)'",
                    '<div>'
                    . '<strong>$1</strong>: '
                    . '<span>'
                    . '<span id="form_decrypted_' . $index . '-asterix">***</span>'
                    . '<span id="form_decrypted_' . $index . '-plain" class="d-none">$2</span>'
                    . '</span>'
                    . '</div>'
                    . '<div class="btn-group">'
                    . '<a href="$2" target="_blank" class="btn btn-outline-primary btn-sm js-tooltip" role="button" data-toggle="tooltip" data-placement="top" title="Open $1 in new window"><em class="fas fa-fw fa-external-link-alt"></em></a>'
                    . '<a href="#" class="btn btn-outline-primary btn-sm js-tooltip js-copy" role="button" data-toggle="tooltip" data-placement="top" data-copy="$2" title="Copy $1 to clipboard"><em class="fas fa-fw fa-copy"></em></a>'
                    . '<a href="#" class="btn btn-outline-warning btn-sm js-tooltip js-show-text" for="#form_decrypted_' . $index . '" role="button" data-toggle="button" data-toggle="tooltip" data-placement="top" title="Show $1"><em class="fas fa-fw fa-eye"></em></a>'
                    . '</div>'
                    , $row);
                $row = preg_replace("'^([A-Za-z0-9-_ ]+): (.*)$'",
                    '<div>'
                    . '<strong>$1</strong>: '
                    . '<kbd id="form_decrypted_' . $index . '">'
                    . '<span id="form_decrypted_' . $index . '-asterix">***</span>'
                    . '<span id="form_decrypted_' . $index . '-plain" class="d-none">$2</span>'
                    . '</kbd>'
                    . '</div>'
                    . '<div class="btn-group">'
                    . '<a class="btn btn-outline-primary btn-sm js-tooltip js-copy" role="button" data-toggle="tooltip" data-placement="top" data-copy="$2" title="Copy $1 to clipboard"><em class="fas fa-fw fa-copy"></em></a>'
                    . '<a href="#" class="btn btn-outline-warning btn-sm js-tooltip js-show-text" for="#form_decrypted_' . $index . '" role="button" data-toggle="button" data-toggle="tooltip" data-placement="top" title="Show $1"><em class="fas fa-fw fa-eye"></em></a>'
                    . '</div>'
                    , $row);
                $rows[$index] = $row;
            }
            $data['decryptedNice'] = '<ul class="list-group list-group-flush"><li class="list-group-item d-flex w-100 justify-content-between">' . implode('</li><li class="list-group-item d-flex w-100 justify-content-between">', $rows) . '</li></ul>';
        }
        return $data;
    }
}
