<?php declare(strict_types=1);


namespace App\MessageHandler;


use App\Redis\RedisServiceInterface;
use MessageInfo\RoleDataProvider;
use MessageInfo\UserListAPIDataProvider;

class UserListHandler
{
    private RedisServiceInterface $redisService;

    /**
     * @param \App\Redis\RedisServiceInterface $redisService
     */
    public function __construct(\App\Redis\RedisServiceInterface $redisService)
    {
        $this->redisService = $redisService;
    }

    public function __invoke(UserListAPIDataProvider $userListAPIDataProvider)
    {
        $users = $userListAPIDataProvider->getUsers();
        $laborRole = (new RoleDataProvider())->getLabor();
        foreach ($users as $user) {
            if($user->getRole() === $laborRole) {
                $this->redisService->set( 'user:' . $user->getEmail(), json_encode($user->toArray()));
            }
        }
    }
}
