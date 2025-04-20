<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SetupProject extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:setup {--force : Force setup even if already configured}';

    /**
     * The console command description.
     */
    protected $description = 'Setup the CAD project with modules and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up CAD Project...');
        
        try {
            // Check if .env exists
            if (!File::exists(base_path('.env'))) {
                if (File::exists(base_path('.env.example'))) {
                    File::copy(base_path('.env.example'), base_path('.env'));
                    $this->info('✅ Created .env file from .env.example');
                } else {
                    $this->error('❌ .env.example file not found!');
                    return 1;
                }
            }

            // Generate key if not exists
            if (empty(config('app.key')) || $this->option('force')) {
                $this->info('🔑 Generating application key...');
                Artisan::call('key:generate', ['--force' => true]);
                $this->info('✅ Application key generated!');
            }

            // Test database connection
            $this->info('🔌 Testing database connection...');
            try {
                DB::connection()->getPdo();
                $this->info('✅ Database connection successful!');
            } catch (\Exception $e) {
                $this->warn('⚠️  Database connection failed: ' . $e->getMessage());
                $this->warn('Please configure your database settings in .env file');
            }

            // Run migrations
            $this->info('📊 Running database migrations...');
            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->info('✅ Database migrated successfully!');
            } catch (\Exception $e) {
                $this->error('❌ Database migration failed: ' . $e->getMessage());
                $this->info('You can run migrations manually later with: php artisan migrate');
            }

            // Create storage directories
            $this->info('📁 Creating storage directories...');
            $directories = [
                'storage/app/public/images',
                'storage/app/public/documents',
                'storage/app/public/uploads',
            ];

            foreach ($directories as $dir) {
                $fullPath = storage_path($dir);
                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                    $this->info("✅ Created directory: {$dir}");
                }
            }

            // Create storage link
            $this->info('🔗 Creating storage link...');
            try {
                Artisan::call('storage:link');
                $this->info('✅ Storage link created!');
            } catch (\Exception $e) {
                $this->warn('⚠️  Storage link creation failed: ' . $e->getMessage());
            }

            // Clear and cache configurations
            $this->info('🧹 Clearing and caching configurations...');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('cache:clear');
            
            if (app()->environment('production')) {
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');
                $this->info('✅ Configurations cached for production!');
            }

            // Install Node dependencies if package.json exists
            if (File::exists(base_path('package.json'))) {
                $this->info('📦 Installing Node.js dependencies...');
                if ($this->confirm('Do you want to install Node.js dependencies?', true)) {
                    $this->info('Running npm install...');
                    exec('npm install 2>&1', $output, $returnCode);
                    if ($returnCode === 0) {
                        $this->info('✅ Node.js dependencies installed!');
                    } else {
                        $this->warn('⚠️  Failed to install Node.js dependencies. Run "npm install" manually.');
                    }
                }
            }

            $this->newLine();
            $this->info('🎉 Project setup completed successfully!');
            $this->info('🚀 You can now run: php artisan serve');
            $this->newLine();
            $this->info('Next steps:');
            $this->line('1. Configure your database settings in .env file');
            $this->line('2. Run: php artisan migrate (if not already done)');
            $this->line('3. Run: npm run dev (for frontend assets)');
            $this->line('4. Start the server: php artisan serve');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Setup failed: ' . $e->getMessage());
            return 1;
        }
    }
}
