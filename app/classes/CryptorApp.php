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
        $autofocus = 'decrypted';

        if ($request->getMethod() === 'POST') {
            $autofocus = null;
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
            'autofocus' => $autofocus,
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
        $autofocus = 'secret';

        if ($request->attributes->has('hash') && $request->attributes->get('hash') !== $this->getSecretHash()) {
            $errors['hash'][] = sprintf('Given request hash <code>%s</code> is different than this instance hash.', $request->get('hash'));
        }

        if ($request->getMethod() === 'POST') {
            $autofocus = 'null';
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
            'autofocus' => $autofocus,
        ];
    }

    private function formatData($data)
    {
        $data['decryptedNice'] = null;
        if (preg_match("'([A-Za-z0-9-_ ]+): '", $data['decrypted'])) {
            $rows = explode("\n", $data['decrypted']);
            $response = [];
            foreach ($rows as $index => $row) {
                $row = trim($row);
                $matches = [];
                if (preg_match_all("'^([A-Za-z0-9-_/ ]+): (.*)$'", $row, $matches)) {
                    $response[$index] = [
                        'name' => [
                            'type' => preg_match("'^(fa(r|s|b) fa-([a-z-]+))$'", $matches[1][0]) ? 'icon' : 'text',
                            'value' => $matches[1][0],
                        ],
                        'data' => [
                            'type' => preg_match("'(https?:\/\/[\w\-\.!~#?&=+\*\'\"(),\/]+)'", $matches[2][0]) ? 'link' : 'kbd',
                            'value' => $matches[2][0],
                        ],
                    ];
                } else {
                    $response[$index] = [
                        'name' => [
                            'type' => 'none',
                            'value' => 'text',
                        ],
                        'data' => [
                            'type' => 'text',
                            'value' => $row,
                        ],
                    ];
                }
            }
            $data['decryptedNice'] = $response;
        }
        return $data;
    }
}
