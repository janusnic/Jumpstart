<?php namespace GeneaLabs\Jumpstart\Commands;

use Carbon\Carbon;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Jumpstart extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:jumpstart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up your vanilla Laravel project with auth and other goodies. DO NOT run this if you already have your users table and auth views set up.';

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
        $namespace = $this->getAppNamespace();

        $this->copyJumpstartAsset(__DIR__ . '/../../assets/views/app.blade.php', base_path('resources/views/app.blade.php'));
        $this->copyJumpstartAsset(__DIR__ . '/../../assets/views/home.blade.php', base_path('resources/views/home.blade.php'));
        $this->copyJumpstartAsset(__DIR__ . '/../../assets/views/welcome.blade.php', base_path('resources/views/welcome.blade.php'));
        $this->copyJumpstartAsset(__DIR__ . '/../../assets/views/auth/login.blade.php', base_path('resources/views/auth/login.blade.php'));
        $this->copyJumpstartAsset(__DIR__ . '/../../assets/views/auth/register.blade.php', base_path('resources/views/auth/register.blade.php'));

        $this->copyJumpstartAsset(__DIR__ . '/../../assets/routes.php', app_path('Http/routes.php'));

        $this->copyJumpstartAsset(__DIR__ . '/../../assets/Controllers/HomeController.php', base_path('app/Http/Controllers/HomeController.php'), $namespace);
        $this->copyJumpstartAsset(__DIR__ . '/../../assets/Controllers/WelcomeController.php', base_path('app/Http/Controllers/WelcomeController.php'), $namespace);


    }

    /**
     * @param string $sourceFile
     * @param string $targetFile
     * @param string $namespace
     */
    private function copyJumpstartAsset($sourceFile, $targetFile, $namespace = 'App\\')
    {
        $fileName = basename($targetFile);

        if (File::exists($targetFile)) {
            $fileNameParts = explode('.', $fileName);
            $newFileName = array_shift($fileNameParts) . '-original-' . Carbon::now('UTC')->format('Y-m-d_h:i:s') . '.' . implode('.', $fileNameParts);
            File::move($targetFile, dirname($targetFile) . '/' . $newFileName);
            $this->info("File '{$fileName}' already exists - renamed to '{$newFileName}'.");
        }

        if ($namespace == 'App\\') {
            File::copy($sourceFile, $targetFile);
        } else {
            $sourceFileContent = File::get($sourceFile);
            $targetFileContent = str_replace('namespace App\\', 'namespace ' . $namespace, $sourceFileContent);
            File::put($targetFile, $targetFileContent);
        }

        $this->info("File '{$fileName}' copied.");
    }
}