<?php

namespace Contentify\Commands;

use Config;
use Contentify\Installer;
use Illuminate\Console\Command;
use Str;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs Contentify - no user input required';
    
    /**
     * The installer object
     *
     * @var Installer
     */
    protected $installer;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->installer = new Installer();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->argument('useremail') ?? time().'@contentify.org';
        $userName = $this->argument('username') ?? 'superadmin';
        $userPassword = $this->argument('userpassword') ?? Str::random(10);

        // Check preconditions
        require public_path('install.php');
        
        // Say hello
        $this->info('Welcome to Contentify '.Config::get('app.version'). ' installer!');
        $this->comment('This command installs Contentify without asking for user input.');
        $this->comment('Therefore it does not change the database credentials.');
        
        // Create database and seed it
        $this->info('Creating database...');
        $this->installer->createDatabase();
        $this->info('Creating user roles...');
        $this->installer->createUserRoles();
        $this->info('Creating daemon user...');
        $this->installer->createDaemonUser();
        $this->info('General seeding...');
        $this->installer->createSeed();
        
        // Create super admin account
        $this->info('Creating super admin user...');
        $this->installer->createAdminUser($userName, $userEmail, $userPassword, $userPassword);
        $userPassword = $this->argument('userpassword') ? str_repeat('*', mb_strlen($userPassword)) : $userPassword;

        $headers = ['Property', 'Value'];
        $values = [
            ['Username', $userName],
            ['Email', $userEmail],
            ['Password', $userPassword],
        ];
        $this->table($headers, $values);
        
        // Say goodbye
        $this->info('Installation complete!');
        $this->installer->markAsInstalled();
        $this->installer->sendStatistics();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['useremail', InputArgument::OPTIONAL, 'The email of the super admin account.'],
            ['username', InputArgument::OPTIONAL, 'The username of the super admin account.'],
            ['userpassword', InputArgument::OPTIONAL, 'The password of the super admin account.'],
        ];
    }
}
