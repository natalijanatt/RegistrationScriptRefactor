<?php

declare(strict_types=1);
namespace App\QueryBuilder\Domain\Contracts;

interface QueryBuilderFactoryInterface
{
    public function make(): QueryBuilderInterface;
}