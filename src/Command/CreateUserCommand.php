<?php

namespace App\Command;

use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create User Command',
    aliases: ['app:add-user', 'a:c:u']
)]
class CreateUserCommand extends Command
{
    private bool $requireEmail;
    private bool $requirePassword;
    private bool $requireFirstname;
    private bool $requireLastname;
    private bool $requireAddress;
    private bool $requireCity_id;
    private bool $requireRole;

    public function __construct(readonly UserRepository $userRepository, readonly EntityManagerInterface $entityManager, readonly UserPasswordHasherInterface $userPasswordHarsher, readonly CityRepository $cityRepository)
    {
        $this->requireEmail = true;
        $this->requirePassword = true;
        $this->requireFirstname = true;
        $this->requireLastname = true;
        $this->requireAddress = true;
        $this->requireCity_id = true;
        $this->requireRole = true;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('arg1 : email, arg2 : password, arg3 : firstname, arg4 : lastname, arg5 : address, arg6 : city_id, arg7 : role')
            ->addArgument('email',$this->requireEmail ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User email')
            ->addArgument('password',$this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password')
            ->addArgument('firstname',$this->requireFirstname ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User firstname')
            ->addArgument('lastname',$this->requireLastname ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User lastname')
            ->addArgument('address',$this->requireAddress ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User address')
            ->addArgument('city_id',$this->requireCity_id ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User city_id')
            ->addArgument('role',$this->requireRole ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $address = $input->getArgument('address');
        $city_id = $input->getArgument('city_id');
        $role = $input->getArgument('role');

        if ($email) {
            $io->note(sprintf('You passed an email : %s', $email));
            if ($this->userRepository->findOneBy(['email'=>$email])){
                $io->error('$email is already registered ');
                return Command::FAILURE;
            }
        }
        if ($password) {
            $io->note(sprintf('You passed a password : %s', $password));
        }
        if ($firstname) {
            $io->note(sprintf('You passed a firstname : %s', $firstname));
        }
        if ($lastname) {
            $io->note(sprintf('You passed a lastname : %s', $lastname));
        }
        if ($address) {
            $io->note(sprintf('You passed an address : %s', $address));
        }
        if ($city_id) {
            $io->note(sprintf('You passed a city_id : %s', $city_id));
        }
        if ($role) {
            $io->note(sprintf('You passed a role : %s', $role));
            if ($role!== 'ROLE_ADMIN'&& $role!== 'ROLE_RESTORER'&& $role!== 'ROLE_USER'){
                $io->error('role must be : ROLE_ADMIN, ROLE_RESTORER or ROLE_USER ');
                return Command::FAILURE;
            }
        }

        $userService = new UserService($this->entityManager, $this->userPasswordHarsher, $this->cityRepository);
        $confirm = $io->confirm('Are you sure you want to create the user'.$email.' ?');
        if (!$confirm){
            $io->block('Canceled');
            return Command::INVALID;
        }
        $user=$userService->createUser($email, $role, $password, $firstname, $lastname, $address, $city_id);



        $io->success('You have created a new user : '.$user);

        return Command::SUCCESS;
    }
}
