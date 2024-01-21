<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\UserResetPasswordEmail;

final class NewUserProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private UserResetPasswordEmail $userResetPasswordEmail
    ) {
    }

    /**
     * @param User $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $newUser = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->userResetPasswordEmail->sendNewUserSetPasswordEmail($newUser);

        return $newUser;
    }
}
