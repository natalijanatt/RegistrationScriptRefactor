<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistance;

use App\QueryBuilder\Domain\Contracts\QueryBuilderFactoryInterface;
use App\QueryBuilder\Infrastructure\Expression\Sql;
use mysqli;
use App\Domain\User\User;
use App\Domain\User\UserRepository;

class MysqliUserRepository implements UserRepository
{
    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {}
    public function findByEmail(string $email): ?User
    {

        $qb = $this->queryBuilderFactory->make();
        $row = $qb
            ->table('users')
            ->select(['id', 'email', 'password'])
            ->where('email', '=', $email)
            ->first();

        if ($row === null) {
            return null;
        }

        return new User((int)$row['id'], $row['email'], $row['password']);
    }

    public function save(User $user): int
    {
        $qb = $this->queryBuilderFactory->make();

        $email = $user->getEmail();
        $passwordHash = $user->getPasswordHash();

        return $qb->table('users')
            ->insert(['email'=> $email, 'password'=>$passwordHash, 'created_at'=>Sql::raw("NOW()")]);
    }

    public function findById(int $id): ?User
    {
       $qb = $this->queryBuilderFactory->make();
        $row = $qb
            ->table('users')
            ->select(['id', 'email', 'password'])
            ->where('id', '=', $id)
            ->first();

        if ($row === null) {
            return null;
        }

        return new User((int)$row['id'], $row['email'], $row['password']);
    }
}