<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateSwaggerDocs extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate Swagger API documentation';

    public function handle()
    {
        $this->info('Generating Swagger documentation...');

        // Define the path to your annotations
        $annotationsPath = base_path('app'); // Or any other path where your API files are located

        // Define the output path for the Swagger JSON
        $outputPath = public_path('swagger.json');

        // Generate the Swagger documentation
        $openApi = Generator::scan([$annotationsPath]);

        // Save the generated Swagger JSON to the output path
        file_put_contents($outputPath, $openApi->toJson());

        $this->info('Swagger documentation generated successfully!');
    }
}
