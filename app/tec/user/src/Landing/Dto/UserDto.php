<?php
namespace Tec\User\Landing\Dto;

class UserDto extends DtoBase
{
    public $userId;
    public $email;
    public $phone;
    public $zcode;
    public $fullname;
    public $passhash;
    public $created;
    public $changed;
}
