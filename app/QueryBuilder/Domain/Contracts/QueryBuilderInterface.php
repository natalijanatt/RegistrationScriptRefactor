<?php

declare(strict_types=1);

namespace App\QueryBuilder\Domain\Contracts;

interface QueryBuilderInterface extends SelectableInterface, InsertableInterface
{
    // No extra methods – just a combination of the two interfaces.
}
