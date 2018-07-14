<?php
namespace WBC\Auth\Storage;

abstract class SharedDatabase
{
    /** @var array */
    protected $options = [
        'table' => 'users',
        'username_column' => 'username',
        'password_column' => 'password'
    ];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }
}
