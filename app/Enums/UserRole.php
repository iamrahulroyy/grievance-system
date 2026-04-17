<?php

namespace App\Enums;

enum UserRole: string
{
    case Citizen = 'citizen';
    case Admin = 'admin';
}
