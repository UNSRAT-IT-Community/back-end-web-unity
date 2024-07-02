<?php

namespace App\ThirdParty;


class Internals
{
    /**
     * Get current logged in user id
     * @return int
     */
    public static function getUserContextId(): int
    {
        return $GLOBALS['USER_DATA']->id;
    }

    /**
     * Get current logged in user role_id
     * @return int
     */
    public static function getUserContextUsername(): string
    {
        return $GLOBALS['USER_DATA']->name;
    }

    /**
     * Get current logged in user email
     * @return int
     */
    public static function getUserContextEmail(): string
    {
        return $GLOBALS['USER_DATA']->email;
    }

    /**
     * Get current logged in user role
     * @return int
     */
    public static function getUserContextRole(): string
    {
        return $GLOBALS['USER_DATA']->role_id;
    }

    /**
     * Get current logged in user verified status
     * @return int
     */
    public static function getUserContextVerified(): bool
    {
        return $GLOBALS['USER_DATA']->is_accepted;
    }
}
