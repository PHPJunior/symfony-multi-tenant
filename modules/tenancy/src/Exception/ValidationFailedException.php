<?php

namespace Module\Tenancy\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedException extends \Exception
{
    public function __construct(private readonly ConstraintViolationListInterface $violations)
    {
        parent::__construct('Validation Error', 422);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
