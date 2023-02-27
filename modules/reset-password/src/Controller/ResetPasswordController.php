<?php

namespace Module\ResetPassword\Controller;

use App\Central\Entity\User as CentralUser;
use App\Tenant\Entity\User as TenantUser;
use Doctrine\ORM\EntityManagerInterface;
use Module\ResetPassword\Form\ChangePasswordFormType;
use Module\ResetPassword\Request\ResetPasswordRequest;
use Module\ResetPassword\Service\ResetPasswordService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ResetPasswordService $resetPasswordService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ){
    }

    /**
     * @param ResetPasswordRequest $request
     * @param MailerInterface $mailer
     * @return JsonResponse
     */
    #[Route(path: 'reset-password', name: 'forgot_password_request', methods: ['POST'])]
    public function request(ResetPasswordRequest $request, MailerInterface $mailer): JsonResponse
    {
        $user = $this->getCentralOrTenantUser($request, ['email' => $request->getEmail()]);

        if (!$user) {
            return $this->json(['message' => 'Email not found'], 404);
        }

        try {
            $resetPassword = $this->resetPasswordService->createPasswordReset($user);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('test@test.com', 'Bot'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'token' => $resetPassword->getHashedToken(),
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }

        return $this->json(['message' => 'Email sent']);
    }

    /**
     * @param Request $request
     * @param string $token
     * @return JsonResponse|Response
     */
    #[Route(path: 'reset-password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function reset(Request $request, string $token): JsonResponse|Response
    {
        try {
            $userId = $this->resetPasswordService->validateTokenAndFetchUserId($token);
        } catch (\Exception $e) {
            return $this->render('reset_password/not_found.html.twig', [
                'message' => $e->getMessage(),
            ]);
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user = $this->getCentralOrTenantUser($request, ['id' => $userId]);
            } catch (\Exception $e) {
                return $this->render('reset_password/not_found.html.twig', [
                    'message' => $e->getMessage(),
                ]);
            }

            $this->resetPasswordService->removePasswordReset($token);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($hashedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->render('reset_password/success.html.twig', [
                'message' => 'Password changed successfully',
            ]);
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @param array $criteria
     * @return CentralUser|TenantUser
     */
    private function getCentralOrTenantUser($request, array $criteria): CentralUser|TenantUser
    {
        $entity = $request->attributes->has('tenant') ? TenantUser::class : CentralUser::class;
        return $this->entityManager->getRepository($entity)->findOneBy($criteria);
    }
}
