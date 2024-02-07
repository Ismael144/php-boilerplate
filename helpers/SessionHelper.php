<?php 

namespace App\helpers; 

use App\core\Helper;

class SessionHelper extends Helper 
{
    /**
     * Checks whether a session exists
     *
     * @param string $sessionKey
     * @return boolean
     */
    public function validate(string $sessionKey): bool
    {   
        return isset($_SESSION[$sessionKey]);
    }

    /**
     * Creates session
     *
     * @param string $sessionKey
     * @param mixed $value
     * @return void
     */
    public function createSession(string $sessionKey, mixed $value): void
    {
        $_SESSION[$sessionKey] = $this->filter($value);
    }

    /**
     * Deletes a session
     *
     * @param string $sessionKey
     * @return bool
     */
    public function deleteSession(string $sessionKey)
    {
        return unlink($_SESSION[$sessionKey]);
    }

    /**
     * Destroys all sessions
     *
     * @return void
     */
    public function destroySessions(): void
    {
        session_destroy();
    }

    
}