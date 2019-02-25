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
        $data['encrypted'] = '';
        $data['decrypted'] = $request->request->get('decrypted');

        if ($request->getMethod() === 'POST') {
            if (empty($data['secret'])) {
                $errors['secret'][] = 'Field can not be empty!';
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
            'data' => $data,
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
            'data' => $data,
            'errors' => $errors,
        ];
    }
}
