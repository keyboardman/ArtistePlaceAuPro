<?php

namespace App\Command;

use AllowDynamicProperties;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Uid\Uuid; // Correct use statement for generating UUIDs

#[AllowDynamicProperties]
#[AsCommand(
    name: 'app:add-user',
    description: 'Creates a new user.',
)]
class AddUserCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionFirstname = new Question('Please enter the firstname: ');
        $firstname = $helper->ask($input, $output, $questionFirstname);

        $questionLastname = new Question('Please enter the lastname: ');
        $lastname = $helper->ask($input, $output, $questionLastname);

        $questionEmail = new Question('Please enter the email: ');
        $email = $helper->ask($input, $output, $questionEmail);

        $passwordQuestion = new Question('Please enter the password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Generate a unique token using the correct Uuid class
        $token = Uuid::v4()->toRfc4122();

        // Create a new User entity
        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setAvatarUrl("assets/images/avatar-user/avatar-default.png");
        $user->setToken($token); // Set the generated token

        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        // Persist the User entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Output success message
        $output->writeln('User ' . $email . ' created successfully with token: ' . $token);

        return Command::SUCCESS;
    }
}
