<?php

namespace App\Enums;


enum ServicesType : string{
    case PLUMBER = "plumber";
    case CARPENTER = "carpenter";
    case ELECTRICIAN = 'electrician';
    case CATERING = 'catering';
    case DRIVER = 'driver';
    case ITPERSONAL ='IT personel';
}
