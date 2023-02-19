<?php

class WebUser
{
    public $username = "";
    public $email = "";
    public $id = -1;
    public $role = "";
    public function __construct($username, $email, $id, $role)
    {
        $this->username = $username;
        $this->email = $email;
        $this->id = $id;
        $this->role = $role;
    }

    public static function getCurrentUser(): WebUser|null
    {
        if (isset($_SESSION['currentuser'])) {
            return $_SESSION['currentuser'];
        }
        return null;
    }

    public static function getCurrentUserId(): int
    {
        $user = WebUser::getCurrentUser();
        if ($user != null) {
            return $user->id;
        }
        return -1;
    }

    public static function alternatifGetCurrentUserId(): int
    {
        if (isset($_SESSION['currentuser'])) {
            echo "user found";
            return $_SESSION['currentuser']->id;
        }
        echo "user not found";
        return -1;
    }

    public static function isConnected(): bool
    {
        return WebUser::getCurrentUser() != null;
    }

    public static function isAdmin(): bool
    {
        $user = WebUser::getCurrentUser();
        if ($user != null) {
            return $user->role == "admin";
        }
        return false;
    }

    public static function getLikedArticles(): array|null
    {
        $dbresult = Database::UserDB()->getWhere(array('liked_articles'), array('id' => WebUser::getCurrentUserId()));
        if (!is_countable($dbresult) || count($dbresult) == 0) {
            return null;
        }
        return json_decode($dbresult[0]['liked_articles']);
    }

}

?>