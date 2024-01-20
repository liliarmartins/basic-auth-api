<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserResetPasswordEmail
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private ContainerBagInterface $params
    ) {
    }

    private function setUserToken(User $user)
    {
        $token = sha1(random_bytes(32));
        $user->setPasswordResetToken($token);
        $user->setPasswordResetTokenExpiresAt(new \DateTimeImmutable('+48 hours'));

        $this->entityManager->flush();

        return $token;
    }

    public function sendNewUserSetPasswordEmail(User $user)
    {
        $token = $this->setUserToken($user);

        $email = (new Email())
            ->from($this->params->get('admin_email'))
            ->to($user->getEmail())
            ->subject('Welcome to My System')
            ->html(
                '<p>Welcome, '.$user->getEmail().'!</p>'.
                '<p>Please use the link below to set up your new account password. '.
                    'The link is only valid for 48 hours.<br />'.
                    '<a href="'.$this->params->get('reset_password_front_url').$token.'">activate account</a>'.
                '</p>'.
                '<p>If the token has expired, please reply to this email</p>'
            )
        ;

        $this->mailer->send($email);
    }
}
