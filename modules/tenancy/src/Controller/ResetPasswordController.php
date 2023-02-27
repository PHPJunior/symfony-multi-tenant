<?php

namespace Module\Tenancy\Controller;

use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

//#[Route('/reset-password')]
//class ResetPasswordController extends AbstractController
//{
//    use ResetPasswordControllerTrait;
//
//    public function __construct(
//        private ResetPasswordHelperInterface $resetPasswordHelper,
//        private EntityManagerInterface $entityManager
//    ) {
//    }
//
//    /**
//     * Display & process form to request a password reset.
//     */
//    #[Route('', name: 'app_forgot_password_request')]
//    public function request(ResetPasswordRequest $request, MailerInterface $mailer): \Symfony\Component\HttpFoundation\JsonResponse
//    {
//        $user = $request->attributes->has('tenant') ? TenantUser::class : CentralUser::class;
//        $user = $this->entityManager->getRepository($user)->findOneBy([
//            'email' => $request->getEmail(),
//        ]);
//
//        if (!$user) {
//            return $this->json([
//                'message' => 'Email not found',
//            ], 404);
//        }
//
//        try {
//            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
//            dd($resetToken);
//        } catch (ResetPasswordExceptionInterface $e) {
//            return $this->json([
//                'message' => sprintf(
//                    '%s - %s',
//                    ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE,
//                    $e->getReason()
//                ),
//            ], 500);
//        }
//
//        $email = (new TemplatedEmail())
//            ->from(new Address('test@test.com', 'Bot'))
//            ->to($user->getEmail())
//            ->subject('Your password reset request')
//            ->htmlTemplate('reset_password/email.html.twig')
//            ->context([
//                'resetToken' => $resetToken,
//            ])
//        ;
//
//        $mailer->send($email);
//
//        // Store the token object in session for retrieval in check-email route.
//        $this->setTokenObjectInSession($resetToken);
//
//        return $this->json([
//            'message' => 'Email sent',
//        ], 200);
//    }
//
//    /**
//     * Confirmation page after a user has requested a password reset.
//     */
//    #[Route('/check-email', name: 'app_check_email')]
//    public function checkEmail(): Response
//    {
//        // Generate a fake token if the user does not exist or someone hit this page directly.
//        // This prevents exposing whether or not a user was found with the given email address or not
//        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
//            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
//        }
//
//        return $this->render('reset_password/check_email.html.twig', [
//            'resetToken' => $resetToken,
//        ]);
//    }
//
//    /**
//     * Validates and process the reset URL that the user clicked in their email.
//     */
//    #[Route('/reset/{token}', name: 'app_reset_password')]
//    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
//    {
//        if ($token) {
//            // We store the token in session and remove it from the URL, to avoid the URL being
//            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
//            $this->storeTokenInSession($token);
//
//            return $this->redirectToRoute('app_reset_password');
//        }
//
//        $token = $this->getTokenFromSession();
//        if (null === $token) {
//            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
//        }
//
//        try {
//            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
//        } catch (ResetPasswordExceptionInterface $e) {
//            $this->addFlash('reset_password_error', sprintf(
//                '%s - %s',
//                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
//                $e->getReason()
//            ));
//
//            return $this->redirectToRoute('app_forgot_password_request');
//        }
//
//        // The token is valid; allow the user to change their password.
//        $form = $this->createForm(ChangePasswordFormType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // A password reset token should be used only once, remove it.
//            $this->resetPasswordHelper->removeResetRequest($token);
//
//            // Encode(hash) the plain password, and set it.
//            $encodedPassword = $passwordHasher->hashPassword(
//                $user,
//                $form->get('plainPassword')->getData()
//            );
//
//            $user->setPassword($encodedPassword);
//            $this->entityManager->flush();
//
//            // The session is cleaned up after the password has been changed.
//            $this->cleanSessionAfterReset();
//
//            return $this->redirectToRoute('app_home');
//        }
//
//        return $this->render('reset_password/reset.html.twig', [
//            'resetForm' => $form->createView(),
//        ]);
//    }
//
//    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
//    {
//
//    }
//}
