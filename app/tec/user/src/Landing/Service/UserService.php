<?php
namespace Tec\User\Landing\Service;

use Gap\Security\PasshashProvider;
use Gap\Valid\ValidPassword;

use Tec\User\Landing\Dto\RegDto;
use Tec\User\Landing\Dto\UserDto;
use Tec\User\Landing\Repo\UserRepo;

class UserService extends ServiceBase
{
    private $userRepo;

    public function reg(RegDto $regDto): void
    {
        $regDto->password = trim($regDto->password);
        $this->validatePassword($regDto->password);

        $userDto = new UserDto([
            'email' => $regDto->email,
            'phone' => $regDto->phone,
            'zcode' => $regDto->zcode,
            'fullname' => $regDto->fullname,
            'passhash' => (new PasshashProvider())->hash($regDto->password)
        ]);
        $this->getUserRepo()->create($userDto);
    }

    private function validatePassword($password): void
    {
        (new ValidPassword())->assert($password);
    }

    private function getUserRepo(): UserRepo
    {
        if ($this->userRepo) {
            return $this->userRepo;
        }

        $this->userRepo = new UserRepo($this->getDmg());
        return $this->userRepo;
    }
}
