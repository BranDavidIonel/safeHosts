<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Host;
use App\Models\Category;
use App\Models\UserCustomList;

class AddToCustomList extends Command
{
    protected $signature = 'hosts:add-custom';
    protected $description = 'Manually add domains to the user custom blocklist';

    public function handle()
    {
        $this->info("Adding domains to the custom blocklist (User ID: 1, Category ID: 1)");
        $this->info("Type 'no' to stop.");

        while (true) {
            // Ask for a domain
            $domain = $this->ask('Enter a domain to block');

            // Stop if the user types "no"
            if (strtolower($domain) === 'no') {
                $this->info("Stopping domain entry");
                break;
            }

            // Validate domain format
            if (!filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
                $this->error("Invalid domain format. Try again.");
                continue;
            }

            // Insert into Hosts table (if not exists)
            $host = Host::firstOrCreate(
                ['domain' => $domain],  // Check if domain exists
                ['category_id' => 1, 'source' => 'manual'] // Assign category_id = 1 and source
            );

            // Check if the host is already in the user_custom_lists table
            $exists = UserCustomList::where('user_id', 1)
                ->where('host_id', $host->id)
                ->exists();

            if ($exists) {
                $this->warn("Domain '$domain' is already in the custom blocklist.");
                continue;
            }

            // Insert into user_custom_lists
            UserCustomList::create([
                'user_id'     => 1,
                'host_id'     => $host->id,
            ]);

            $this->info("Added: $domain");
        }

        $this->info("Custom blocklist update completed!");
    }
}
