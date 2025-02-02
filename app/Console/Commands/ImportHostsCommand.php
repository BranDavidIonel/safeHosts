<?php

namespace App\Console\Commands;
use App\Models\Host;
use App\Models\Category;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportHostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosts:import';
    private $urlSearch = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import blocked hosts from a specified URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //url=https://raw.githubusercontent.com/StevenBlack/hosts/refs/heads/master/alternates/porn/hosts
        //$urlSearch = $this->argument('url');
        $urlSearch = $this->ask('What is your URL?');
        $category = $this->ask('What is your category?');
        $descriptionCategory = $this->ask('What is your description category?');
        $this->info('Fetching hosts file...');
        // get file
        $response = Http::get($urlSearch);
        if (!$response->successful()) {
            $this->error('Failed to fetch the hosts file!');
            return;
        }
        $lines = explode("\n", $response->body());
        $blockedDomains = [];
        $blockedDomains = $this->parsTheFile($lines, $blockedDomains);

        if (empty($blockedDomains)) {
            $this->warn('No domains found in the file.');
            return;
        }

        //create category/host if don't exist
        $category = Category::firstOrCreate(['name' => $category], ['description' => $descriptionCategory]);

        $countsHostsBefore = Host::count();
        $this->info("Importing " . count($blockedDomains) . " domains into the database...");
        foreach ($blockedDomains as $domain) {
            Host::firstOrCreate(
                ['domain' => $domain], //check domain exist
                ['category_id' => $category->id, 'source' => $urlSearch]
            );
        }
        $countsHostsAfter = Host::count();
        $countsHostsNew = $countsHostsAfter - $countsHostsBefore;
        $this->info("counts hosts before: " . $countsHostsBefore);
        $this->info("counts host after: " . $countsHostsAfter);
        $this->info("counts host new: " . $countsHostsNew);

        $this->info('Import completed successfully!');
    }


    private function parsTheFile(array $lines, array $blockedDomains): array
    {
        $excludedDomains = [
            'localhost',
            'localhost.localdomain',
            'local',
            '0.0.0.0',
            'broadcasthost',
            'ip6-localhost',
            'ip6-loopback',
            'ip6-localnet',
            'ip6-mcastprefix',
            'ip6-allnodes',
            'ip6-allrouters',
            'ip6-allhosts'
        ];
        foreach ($lines as $line) {
            $line = trim($line);
            //ignore empty rows and comments lines
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }
            //check the line have a domain valid
            if (preg_match('/^(0\.0\.0\.0|127\.0\.0\.1)\s+([a-zA-Z0-9.-]+)$/', $line, $matches)) {
                $domainName = $matches[2];
                if(!in_array($domainName, $excludedDomains)) {
                    $blockedDomains[] = $domainName;
                }
            }
        }
        return $blockedDomains;
    }
}
