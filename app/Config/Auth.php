<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public $sym = "+";
    public $duration = 10;
    public $unit = 'seconds';
    public $symRefresh = "+";
    public $durationRefresh = 20;
    public $unitRefresh = 'seconds';
}
