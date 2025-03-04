<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Twilio extends BaseConfig
{
    public $sid = '';
    public $token = '';
    public $from = '';

    public function __construct()
    {
        $this->sid = getenv('TWILIO_SID');
        $this->token = getenv('TWILIO_AUTH_TOKEN');
        $this->from = getenv('TWILIO_PHONE_NUMBER');
    }
}
