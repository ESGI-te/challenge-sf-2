<?php

namespace App\Service;
use Exception;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class MailService
{
    public function __construct($sendinblueApiKey) {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $sendinblueApiKey);
        $this->apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
    }

    public function sendMail($parameters){
        $email = new SendSmtpEmail($parameters);

        try {
            $this->apiInstance->sendTransacEmail($email);
        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }
}