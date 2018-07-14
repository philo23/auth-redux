<?php
namespace WBC\Auth\Storage;

use WBC\Auth\UserContainer;

class File implements StorageInterface
{
    /** @var resource */
    private $fh;
    /** @var array */
    private $options = [
        'column_separator' => "\t",
        'username_column_index' => 0,
        'password_column_index' => 1
    ];

    /**
     * @param resource $fh
     * @param array $options
     * @throws \Exception
     */
    public function __construct($fh, array $options = [])
    {
        $this->fh = $fh;
        $this->options = array_merge($this->options, $options);
    }

    public function getUser($username)
    {
        rewind($this->fh);
        while (($line = fgets($this->fh)) !== false) {
            $data = explode($this->options['column_separator'], rtrim($line));
            if ($data[$this->options['username_column_index']] === $username) {
                $user = new UserContainer(
                    $data[$this->options['username_column_index']],
                    $data[$this->options['password_column_index']]
                );
                $data[$this->options['password_column_index']] = '';
                $user->data = $data;

                return $user;
            }
        }

        return null;
    }

    public function updatePassword($username, $hash)
    {
        throw new \BadMethodCallException('Password updating is unsupported with File storage.');
    }
}