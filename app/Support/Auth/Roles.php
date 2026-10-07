<?php

namespace App\Support\Auth;

/**
 * The three roles from SRS section 6. Listed here rather than read from the
 * database so validation rules and the generated API spec do not depend on
 * which rows exist.
 */
final class Roles
{
    public const ADMINISTRATOR = 'Administrator';

    public const CONTENT_EDITOR = 'Content editor';

    public const SALES = 'Sales';

    public const ALL = [self::ADMINISTRATOR, self::CONTENT_EDITOR, self::SALES];
}
