<?php

require 'Address.php';

/**
 * @property string $email
 */
class User{
    public string $name = 'Henri Azevedo';
    public array $values = [1 => 'param1'];
    public Address $address;

    public array $data = [];

    public function __construct()
    {
        $this->address = new Address();
        $this->data = ['email'];
        $this->email = 'hnr.azevedo@gmail.com';
    }

    public function getVars(): array
    {
        $vars = [];
        foreach($this->data as $var => $value){
            $vars[$var] = $value;
        }
        return $vars;
    }

    public function __set(string $field, $value)
    {
        $this->data[$field] = $value;
    }

    public function __get(string $field)
    {
        return $this->data[$field];
    }
        
}
