<?php

namespace App\Enums;


enum ServicesType: string
{
    case PLUMBER = "plumbing";
    case CARPENTER = "carpentry";
    case ELECTRICIAN = 'electrician';
    case CATERING = 'catering';
    case DRIVER = 'driver';
    case ITPERSONAL = 'IT personnel';
}
