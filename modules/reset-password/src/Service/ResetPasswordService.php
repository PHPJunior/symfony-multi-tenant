<?php

namespace Module\ResetPassword\Service;

use App\Central\Entity\User as CentralUser;
use App\Tenant\Entity\User as TenantUser;
use Exception;
use Module\ResetPassword\Entity\ResetPassword;
use Module\ResetPassword\Repository\ResetPasswordRepository;

class ResetPasswordService
{
    public function __construct(
        private readonly ResetPasswordRepository $resetPasswordRepository,
    ){
    }

    /**
     * @param CentralUser|TenantUser $user
     * @return ResetPassword
     * @throws Exception
     */
    public function createPasswordReset(CentralUser|TenantUser $user): ResetPassword
    {
        $this->deletePasswordResetRequests($user);

        $resetPassword = new ResetPassword();
        $resetPassword->setUserId($user->getId());
        $resetPassword->setHashedToken(bin2hex(random_bytes(32)));
        $resetPassword->setRequestedAt(new \DateTime());
        $resetPassword->setExpiresAt(new \DateTime('+7 day'));

        $this->resetPasswordRepository->save($resetPassword, true);
        return $resetPassword;
    }

    /**
     * @param CentralUser|TenantUser $user
     * @return void
     */
    public function deletePasswordResetRequests(CentralUser|TenantUser $user): void
    {
        $passwordResetRequests = $this->resetPasswordRepository->findBy(['userId' => $user->getId()]);
        foreach ($passwordResetRequests as $passwordResetRequest) {
            $this->resetPasswordRepository->remove($passwordResetRequest, true);
        }
    }

    /**
     * @param string $token
     * @return ResetPassword|null
     */
    public function getPasswordReset(string $token): ?ResetPassword
    {
        return $this->resetPasswordRepository->findOneBy(['hashedToken' => $token]);
    }

    /**
     * @param string $token
     * @return void
     */
    public function removePasswordReset(string $token): void
    {
        $resetPassword = $this->getPasswordReset($token);
        if ($resetPassword) {
            $this->resetPasswordRepository->remove($resetPassword, true);
        }
    }

    public function validateTokenAndFetchUserId(string $token): int
    {
        $resetPassword = $this->getPasswordReset($token);
        if (!$resetPassword) {
            throw new Exception('Invalid token');
        }
        if ($resetPassword->getExpiresAt() < new \DateTime()) {
            throw new Exception('Token expired');
        }
        return $resetPassword->getUserId();
    }
}
