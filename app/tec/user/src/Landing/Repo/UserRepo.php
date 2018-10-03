<?php
namespace Tec\User\Landing\Repo;

use Tec\User\Landing\Dto\UserDto;

use Gap\Valid\ValidEmail;
use Gap\Valid\ValidNotEmpty;
use Gap\Valid\ValidWord;
use Gap\Dto\DateTime;

class UserRepo extends RepoBase
{
    private $table = 'user';

    public function create(UserDto $userDto): void
    {
        $userDto->email = trim($userDto->email);
        $userDto->phone = trim($userDto->phone);
        $userDto->zcode = trim($userDto->zcode);
        $userDto->fullname = trim($userDto->fullname);

        if (empty($userDto->userId)) {
            $userDto->userId = $this->cnn->zid();
        }

        (new ValidEmail())->assert($userDto->email);
        (new ValidNotEmpty())->assert($userDto->phone);
        //(new ValidNotEmpty())->assert($userDto->zcode);
        (new ValidWord())
            ->setMin(5)
            ->setMax(64)
            ->assert($userDto->zcode);

        $this->assertNotExists('userId', $userDto->userId);
        $this->assertNotExists('email', $userDto->email);
        $this->assertNotExists('phone', $userDto->phone);
        $this->assertNotExists('zcode', $userDto->zcode);
        $this->assertNotExists('fullname', $userDto->fullname);

        $now = new DateTime();
        $userDto->created = $now;
        $userDto->changed = $now;

        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'userId',
                'email',
                'phone',
                'zcode',
                'fullname',
                'passhash',
                'created',
                'changed'
            )
            ->value()
                ->addStr($userDto->userId)
                ->addStr($userDto->email)
                ->addStr($userDto->phone)
                ->addStr($userDto->zcode)
                ->addStr($userDto->fullname)
                ->addStr($userDto->passhash)
                ->addDateTime($userDto->created)
                ->addDateTime($userDto->changed)
            ->end()
            ->execute();
    }

    private function assertNotExists(string $field, $val): void
    {
        $existed = $this->cnn->ssb()
            ->select($field)
            ->from($this->table)->end()
            ->where()
                ->expect($field)->equal()->str($val)
            ->end()
            ->fetchAssoc();

        if ($existed) {
            throw new \Exception($field . ': ' . $val . ' already exists in ' . $this->table);
        }
    }
}
