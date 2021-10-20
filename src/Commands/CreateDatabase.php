<?php

namespace AseanCode\DbCommands\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create
        {connection? : Defined database connection engine }
        {--all : Create databases for all environments }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new three databases in production, staging and development';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $envOption = $this->option('env') ? app()->environmentFile() : null;
        $connection = $this->argument('connection') ? $this->argument('connection') : config('database.default');
        $all_option = $this->option('all') ? true : false;
        if($this->option('env')==='production'){
            if( $this->confirm("Are you sure to create all the databases in $envOption mode? make sure you backup before to do this !") ){
                (strtolower($connection)==='sqlite') ? $this->executeCreateSQLiteDatabase($connection, $envOption, $all_option):
                $this->executeCreateDatabase($connection, $envOption, $all_option);
            }else{
                $this->warn('Operation is cancelled...!');
            }
        }else{
            (strtolower($connection)==='sqlite') ? $this->executeCreateSQLiteDatabase($connection, $envOption, $all_option):
            $this->executeCreateDatabase($connection, $envOption, $all_option);
        }
    }

    protected function executeCreateSQLiteDatabase($connection=null, $envOption=null, $all_option = null){
        $this->info("Initialize creating databases go now ...");
        $envs = $all_option ? [".env",".env.testing",".env.development",".env.staging",".env.production"] : [$envOption];
        $bar = $this->output->createProgressBar(count($envs));
        foreach($envs as $env){
            try{
                try {
                    $environmentFileEnv = $all_option ? $env : $envOption;
                    $dotenv = Dotenv::createMutable( app()->environmentPath(), $environmentFileEnv)->safeLoad();
                    $this->warn("\nDatabase Name: ".$dotenv["DB_DATABASE"]);
                } catch (InvalidPathException $e) {
                    $this->error('\nThe path environment file is invalid: '.$e->getMessage());
                    continue;
                } catch (InvalidFileException $e) {
                    $this->error('\nThe environment file is invalid: '.$e->getMessage());
                    continue;
                }
                $databaseName = $dotenv["DB_DATABASE"];
                exec("touch {$databaseName}");
                $bar->advance();
                $this->info("\nCreated databases successfully ...");
            }catch(\Illuminate\Database\QueryException $e){
                $this->warn("\nFailed to create!");
                $this->error($e->getMessage());
            }
        }
        // $bar->finish();
    }

    protected function executeCreateDatabase($connection=null, $envOption=null, $all_option = null){
        $this->info("Initialize creating databases go now ...");
        $envs = $all_option ? [".env",".env.testing",".env.development",".env.staging",".env.production"] : [$envOption];
        $bar = $this->output->createProgressBar(count($envs));
        foreach($envs as $env){
            try{
                try {
                    $environmentFileEnv = $all_option ? $env : $envOption;
                    $dotenv = Dotenv::createMutable( app()->environmentPath(), $environmentFileEnv )->safeLoad();
                    $this->warn("\nDatabase Name: ".$dotenv["DB_DATABASE"]);
                } catch (InvalidPathException $e) {
                    $this->error('\nThe path environment file is invalid: '.$e->getMessage());
                    continue;
                } catch (InvalidFileException $e) {
                    $this->error('\nThe environment file is invalid: '.$e->getMessage());
                    continue;
                }
                $databaseName = $dotenv["DB_DATABASE"];
                $charset = $dotenv["CHARSET"];
                $collation = $dotenv["COLLATION"] ;
                config(["database.connections.mysql.database" => null]); //clear existing database configuration to enable initialize making database
                $this->info("\nCreating this database naming, ".$databaseName." ...");
                $query = "CREATE DATABASE $databaseName CHARACTER SET $charset COLLATE $collation;";
                DB::statement($query);
                $this->info($query);
                $bar->advance();
                $this->info("\nCreated databases successfully ...");
            }catch(\Illuminate\Database\QueryException $e){
                $this->warn("\nFailed to create!");
                $this->error($e->getMessage());
            }
        }
        // $bar->finish();
    }
}
