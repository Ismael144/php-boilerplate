<?php 

namespace App\constants; 

enum FileDirPathEnum: string 
{

    /* For database purposes */
    case PATH_TO_MYSQLDUMP = "C:/xampp/mysql/bin/";
    case PATH_TO_BKUP_DIR = __DIR__."/backup/";
    case PATH_TO_SETTINGS_INI = "/settings/settings.ini/";
}