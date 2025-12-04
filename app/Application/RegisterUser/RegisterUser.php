<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Domain\Logging\Logger;
use App\Domain\Notification\EmailSender;
use App\Domain\Persistence\TransactionManager;
use App\Domain\Security\PasswordHasher;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Exception;

class RegisterUser
{
    public function __construct(
        private readonly UserRepository        $userRepository,
        private readonly PasswordHasher        $passwordHasher,
        private readonly EmailSender           $emailSender,
        private readonly TransactionManager    $transactionManager,
        private readonly Logger                $logger,
    )
    {}

    /**
     * @throws Exception
     */
    public function execute(RegisterUserRequest $request): RegisterUserResponse
    {

        $existing = $this->userRepository->findByEmail($request->email);

        if($existing !== null){
            return new RegisterUserResponse(false, null, 'email_exists');
        }

        $password_hash = $this->passwordHasher->hash($request->password);
        $user = new User(null, $request->email, $password_hash);

        try {
            $this->transactionManager->beginTransaction();
            
            $userId = $this->userRepository->save($user);
            
            $this->transactionManager->commit();
            
            try {
                $this->emailSender->send(
                    $request->email,
                    'Dobro došli',
                    'Dobro došli na naš sajt. Potrebno je samo da potvrdite email adresu...'
                );
            } catch (Exception $e) {
                $this->logger->warning('Failed to send welcome email to {email}', [
                    'email' => $request->email,
                    'exception' => $e,
                ]);
            }
            
            return new RegisterUserResponse(true, $userId, null);
        } catch (Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }
    }
}