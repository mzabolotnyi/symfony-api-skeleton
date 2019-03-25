<?php

namespace App\Command\User;

use App\Constant\User\Role;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserCommand extends Command
{
    const NAME = 'user:create-admin';

    /** @var UserManagerInterface */
    private $userManager;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(UserManagerInterface $userManager, ValidatorInterface $validator)
    {
        $this->userManager = $userManager;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Command for creating admin user')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password')
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = $this->userManager->createUser();
        $user->setEmail($email)
            ->setPlainPassword($password)
            ->addRole(Role::ROLE_ADMIN);

        $this->userManager->updateUser($user);

        $io = new SymfonyStyle($input, $output);
        $io->success(
            "\nCreated new admin\nEmail: $email"
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {

                $violations = $this->validator->validate(
                    ['email' => $email],
                    new Collection(['email' => [new Email(), new NotBlank()]])
                );

                if (count($violations) > 0) {
                    throw new \Exception('Please enter correct email');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {

                $violations = $this->validator->validate(
                    ['password' => $password],
                    new Collection(['password' => [new NotBlank()]])
                );

                if (count($violations) > 0) {
                    throw new \Exception('Invalid password value');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}