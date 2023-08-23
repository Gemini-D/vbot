<?php

declare(strict_types=1);

namespace Hanson\Vbot\Contact;

class Officials extends Contacts
{
    public function isOfficial($verifyFlag)
    {
        return ($verifyFlag & 8) != 0;
    }
}
