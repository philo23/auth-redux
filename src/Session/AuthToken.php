<?php
namespace WBC\Auth\Session;

use WBC\Auth\UserContainer;

class AuthToken implements SessionInterface
{
    /** @var \PDO */
    private $db;
    /** @var string */
    private $table;
    /** @var string */
    private $cookie_name;
    /** @var \DateInterval */
    private $expires_in;

    public function __construct(\PDO $db, $table, $cookie_name, \DateInterval $expires_in)
    {
        $this->db = $db;
        $this->table = $table;
        $this->cookie_name = $cookie_name;
        $this->expires_in = $expires_in;
    }

    /**
     * @param string $token
     * @return array|null
     */
    private function lookupToken ($token)
    {
        $now = new \DateTime();

        $sql = sprintf(
            "SELECT username, created_at FROM `%s` WHERE token = ? AND expires_at > ?",
            $this->table
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $token,
            $now->format('Y-m-d H:i:s')
        ]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        return $data;
    }

    public function setUser(UserContainer $user)
    {
        $token = bin2hex(random_bytes(16));
        $username = $user->getUsername();

        $created_at = new \DateTimeImmutable();
        $created_at_f = $created_at->format('Y-m-d H:i:s');
        $expires_at = $created_at->add($this->expires_in);
        $expires_at_f = $expires_at->format('Y-m-d H:i:s');

        $sql = sprintf(
            "INSERT INTO `%s` (token, username, created_at, expires_at) VALUES (?, ?, ?, ?)",
            $this->table
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $token,
            $username,
            $created_at_f,
            $expires_at_f
        ]);

        setcookie(
            $this->cookie_name,
            $token,
            $expires_at->getTimestamp()
        );
        $_COOKIE[$this->cookie_name] = $token;
    }

    public function getUsername()
    {
        if (empty($_COOKIE[$this->cookie_name])) {
            return null;
        }

        $data = $this->lookupToken($_COOKIE[$this->cookie_name]);
        return $data ? $data['username'] : null;
    }

    public function getUserLoggedInAt()
    {
        if (empty($_COOKIE[$this->cookie_name])) {
            return null;
        }

        $data = $this->lookupToken($_COOKIE[$this->cookie_name]);
        return $data ? $data['created_at'] : null;
    }

    public function clearUser()
    {
        if (empty($_COOKIE[$this->cookie_name])) {
            return;
        }

        $sql = sprintf(
            "DELETE FROM `%s` WHERE token = ?",
            $this->table
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $_COOKIE[$this->cookie_name]
        ]);

        setcookie(
            $this->cookie_name,
            '',
            time() - 60 * 60 * 24
        );
        unset($_COOKIE[$this->cookie_name]);
    }
}
