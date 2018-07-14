<?php
namespace WBC\Auth\Storage;

use WBC\Auth\UserContainer;

class LegacyMySQL extends SharedDatabase implements StorageInterface
{
    /** @var null|resource */
    private $db;

    /**
     * LegacyMySQL constructor.
     * @param null|resource $db
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct($db = null, array $options = [])
    {
        parent::__construct($options);

        if (!is_null($db) && !is_resource($db)) {
            throw new \InvalidArgumentException(
                'First argument must be null or a MySQL link resource.'
            );
        }

        $this->db = $db;
    }

    public function getUser($username)
    {
        $table = $this->options['table'];
        $username_column = $this->options['username_column'];
        $password_column = $this->options['password_column'];

        $sql = sprintf(
            "SELECT * FROM `{$table}` WHERE `{$username_column}` = '%s'",
            mysql_real_escape_string($username)
        );
        $result = $this->query($sql);
        if (mysql_num_rows($result) == 0) {
            return null;
        }
        $data = mysql_fetch_assoc($result);

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

        $sql = sprintf(
            "UPDATE `{$table}` SET `{$password_column}` = '%s' WHERE `{$username_column}` = '%s'",
            mysql_real_escape_string($hash),
            mysql_real_escape_string($username)
        );
        $this->query($sql);
    }

    /**
     * Wraps mysql_query because passing NULL to the second parameter
     * is not the same as passing no parameter at all
     * @param string $sql
     * @return resource
     */
    private function query($sql)
    {
        if (is_resource($this->db)) {
            return mysql_query($sql, $this->db);
        } else {
            return mysql_query($sql);
        }
    }
}
