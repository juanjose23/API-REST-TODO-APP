<?php

namespace App\console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

// @author: Juanjo Ruiz (Github: jjruizdeveloper | youtube: @gogodev | discord: juanjo.ruiz)

class DDDStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ddd {context : The bounded context, such as admin, lms or job_request} {entity : The entity to create the DDD structure, books for example}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates DDD folder structure for the given entity';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $uri = base_path('src/modules/'. $this->argument('context') .'/'. $this->argument('entity'));
        $this->info('Creating structure...');

        File::makeDirectory($uri . '/Domain', 0755, true, true);
        $this->info($uri . '/Domain');

        File::makeDirectory($uri . '/Domain/Entities', 0755, true, true);
        $this->info($uri . '/Domain/Entities');

        File::makeDirectory($uri . '/Domain/ValueObjects', 0755, true, true);
        $this->info($uri . '/Domain/ValueObjects');

        File::makeDirectory($uri . '/Domain/Contracts', 0755, true, true);
        $this->info($uri . '/Domain/contracts');

        File::makeDirectory($uri . '/Application', 0755, true, true);
        $this->info($uri . '/Application');

        File::makeDirectory($uri . '/Infrastructure', 0755, true, true);
        $this->info($uri . '/Infrastructure');

        File::makeDirectory($uri . '/Infrastructure/Repositories', 0755, true, true);
        $this->info($uri . '/Infrastructure/Repositories');

        File::makeDirectory($uri . '/Infrastructure/Listeners', 0755, true, true);
        $this->info($uri . '/Infrastructure/listeners');

        File::makeDirectory($uri . '/Infrastructure/Events', 0755, true, true);
        $this->info($uri . '/Infrastructure/Events');

        File::makeDirectory($uri . '/Presentation', 0755, true, true);
        $this->info($uri . '/Presentation');
        File::makeDirectory($uri . '/Presentation/Controllers', 0755, true, true);
        $this->info($uri . '/Presentation/Controllers');

        File::makeDirectory($uri . '/Presentation/Routes', 0755, true, true);
        $this->info($uri . '/Presentation/Routes');
        // api.php
        $content = "<?php\n\n//use Src\\".$this->argument('context')."\\".$this->argument('entity')."\\Infrastructure\controllers\ExampleGETController;\n\n// Simpele route example\n// Route::get('/', [ExampleGETController::class, 'index']);\n\n//Authenticathed route example\n// Route::middleware(['auth:sanctum','activitylog'])->get('/', [ExampleGETController::class, 'index']);";
        File::put($uri . '/Presentation/routes/api.php', $content);
        $this->info('Routes entry point added in ' . $uri . 'Presentation/routes/api.php' );
        // local api.php added to main api.php
        $content = "\nRoute::prefix('" . $this->argument('context') . "_" .$this->argument('entity') . "')->group(base_path('src/". $this->argument('context') . "/" .$this->argument('entity') ."/Infrastructure/routes/api.php'));\n";
        File::append(base_path('routes/api.php'), $content);
        $this->info('Module routes linked in main routes directory.');
        $this->info('Structure ' . $this->argument('entity') . ' DDD successfully created.');

        return CommandAlias::SUCCESS;
    }
}
