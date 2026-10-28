<?php

declare(strict_types=1);

namespace App\Admin;

enum LoginResult
{
    case Success;
    case InvalidCredentials;
    case Suspended;
    case Throttled;
}
