<?php
namespace WBC\Auth\Storage;

use WBC\Auth\UserContainer;

class PDO extends SharedDatabase implements StorageInterface
{
    /** @var \PDO */
    private $db;

    public function __construct(\PDO $db, array $options = [])
    {
        parent::__construct($options);
        $this->db = $db;
    }

    public function getUser($username)
    {
        $table = $this->options['table'];
        $username_column = $this->options['username_column'];
        $password_column = $this->options['password_column'];

        $sql = "SELECT * FROM `{$table}` WHERE `{$username_column}` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([ $username ]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }

        $user = new UserContainer($data[$username_column], $data[$password_column]);
        unset($data[$password_column]);
        $user->data = $data;

        return $user;
    }

    public function updatePassword($username, $hash)
    {
        $table = $this->options['table'];
        $username_column = $this->options['username_column'];
        $password_column = $this->options['password_column'];

        $sql = "UPDATE `{$table}` SET `{$password_column}` = ? WHERE `{$username_column}` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $hash,
            $username
        ]);
    }
}
