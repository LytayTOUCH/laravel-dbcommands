<?php

namespace AseanCode\DbCommands\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

class DropDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop
        {connection?* : Defined database with multiple engine }
        {--all : Drop databases for all environments }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all databases of the project';

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
        switch (true) {
            case $this->option('env')==='production':
                $this->confirm("Are you sure to drop the database in $envOption mode? make sure you backup before to do this !")?
                $this->executeDropDatabase($connection, $envOption, $all_option):$this->warn('Operation is cancelled...!');
                break;
            case $all_option:
                $this->confirm("Are you sure to drop all the databases? make sure you backup before to do this !")?
                $this->executeDropDatabase($connection, $envOption, $all_option):$this->warn('Operation is cancelled...!');
                break;
            case !$all_option && $this->option('env')!=='production':
                $this->executeDropDatabase($connection, $envOption, $all_option);
                break;
            default:
                break;
        }
    }

    protected function executeDropDatabase($connection=null, $envOption=null, $all_option = null){
        $this->info("Initialize dropping databases go now ...");
        $envs = $all_option ? [".env",".env.testing",".env.development",".env.staging",".env.production"] : [$envOption];
        $bar = $this->output->createProgressBar(count($envs));
        foreach($envs as $env){
            try{
                try {
                    $environmentFileEnv = $all_option ? $env : $envOption;
                    $dotenv = (new Dotenv(app()->environmentPath(), $environmentFileEnv))->overload();
                    foreach($dotenv as $each){
                        $getAppENVFromDotenv = explode("=",$each);
                        putenv("$getAppENVFromDotenv[0]=$getAppENVFromDotenv[1]");
                    }
                    $this->warn("\nDatabase Name: ".getenv("DB_DATABASE"));
                } catch (InvalidPathException $e) {
                    $this->error('The path environment file is invalid: '.$e->getMessage());
                    continue;
                } catch (InvalidFileException $e) {
                    $this->error('The environment file is invalid: '.$e->getMessage());
                    continue;
                }
                $databaseName = getenv('DB_DATABASE');
                config(["database.connections.mysql.database" => null]); //clear existing database configuration to enable initialize making database
                $this->info("\nDropping this database naming, ".$databaseName." ...");
                $query = "DROP DATABASE $databaseName;";
                DB::statement($query);
                $this->info($query);
                $bar->advance();
                $bar->finish();
                $this->info("\nDropped databases successfully ...");
            }catch(\Illuminate\Database\QueryException $e){
                $this->warn("\nFailed to drop!");
                $this->error($e->getMessage());
                continue;
            }
        }
    }
}
