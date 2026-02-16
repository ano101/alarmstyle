<?php

namespace App\Console\Commands;

use App\Jobs\GenerateSitemap;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml file with all products, categories, landing pages, and regular pages';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        GenerateSitemap::dispatchSync();

        $this->info('Sitemap generated successfully at storage/app/public/sitemap.xml');

        return Command::SUCCESS;
    }
}
