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

#[AllowDynamicProperties] #[AsCommand(
    name: 'app:add-user',
    description: 'Add a short description for your command',
)]
class AddUserCommand extends Command
{
    public function __construct(private readonly  EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a new user.')
        ;
    }

    /*protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }*/
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionUsername = new Question('Please enter the username: ');
        $username = $helper->ask($input, $output, $questionUsername);

        $questionEmail = new Question('Please enter the email: ');
        $email = $helper->ask($input, $output, $questionEmail);

        $passwordQuestion = new Question('Please enter the password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Create a new User entity
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        // You might want to hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        // Persist the User entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Output success message
        $output->writeln('User ' . $username . ' created successfully.');

        return Command::SUCCESS;
    }
}
