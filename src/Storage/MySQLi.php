<?php
namespace WBC\Auth\Storage;

use WBC\Auth\UserContainer;

class MySQLi extends SharedDatabase implements StorageInterface
{
    /** @var \mysqli */
    private $db;

    /**
     * @param \mysqli $db
     * @param array $options
     */
    public function __construct(\mysqli $db, array $options = [])
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
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            return null;
        }
        $data = $result->fetch_assoc();

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
        $stmt->bind_param('ss', $hash, $username);
        $stmt->execute();
    }
}
