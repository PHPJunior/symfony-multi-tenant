<?php

namespace Module\ResetPassword\Request;

use Module\Tenancy\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Please enter your email')]
    #[Assert\Email(message: 'Please enter a valid email')]
    private ?string $email = null;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
