<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Artisan command to scaffold a DDD functional module inside src/modules.
 *
 * Creates a set of folders following the project's DDD conventions.
 */
class DDDCreateModule extends Command
{
    // Command signature and description (English)
    protected $signature = 'ddd:make-module {module} {--domain=auth}';
    protected $description = 'Create a DDD functional subdomain following the project structure.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $module = strtolower($this->argument('module'));
        $domain = strtolower($this->option('domain'));

        if (empty($module)) {
            $this->error('Module name is required.');
            return self::FAILURE;
        }

        $basePath = base_path("src/modules/{$domain}/{$module}");

        $folders = [
            // Application Layer
            'Application',
            'Application/Commands',
            'Application/Dtos',
            'Application/Handlers',
            'Application/Queries',

            // Domain Layer
            'Domain/Contracts',
            'Domain/Entities',
            'Domain/ValueObjects',

            // Infrastructure Layer
            'Infrastructure/Events',
            'Infrastructure/Listeners',
            'Infrastructure/Mail',
            'Infrastructure/Repositories',

            // Presentation Layer
            'Presentation/Controllers',
            'Presentation/Requests',
            'Presentation/Routes',
        ];

        try {
            if (File::isDirectory($basePath)) {
                $this->info("Module '{$module}' already exists at: src/modules/{$domain}/{$module}");
                return self::SUCCESS;
            }

            foreach ($folders as $folder) {
                $path = "{$basePath}/{$folder}";
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0755, true, true);
                }
            }

            // Create a routes file with an English header comment
            $routesPath = "{$basePath}/Presentation/Routes/{$module}.php";
            File::put(
                $routesPath,
                "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Routes for the {$module} module\n"
            );

            $this->info("✔ DDD functional module '{$module}' created at: src/modules/{$domain}/{$module}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('An error occurred while creating the module: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
