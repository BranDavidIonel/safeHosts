<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserCustomList;
use App\Models\Host;
use Carbon\Carbon;

class GenerateHostsFile extends Command
{
    protected $signature = 'hosts:generate-file';
    protected $description = 'Generate a hosts file with custom and standard host records';

    public function handle()
    {
        // Set the filename
        $fileName = storage_path('app/custom_hosts.txt');
        // Get the current date
        $currentDate = Carbon::now()->toDateTimeString();
        // Get all custom user entries (user_id = 1) and associated domains
        $customHosts = UserCustomList::with('host')
            ->where('user_id', 1)
            ->get();

        // Get all hosts from the database
        $hosts = Host::all();
        // Start building the hosts file
        $hostsFileContent = "# This hosts file is a merged collection of hosts from reputable sources,\n";
        $hostsFileContent .= "# with a dash of crowd sourcing via GitHub\n\n";
        $hostsFileContent .= "# Date: $currentDate (UTC)\n";
        $hostsFileContent .= "# Extensions added to this file: custom\n";
        $hostsFileContent .= "# Number of unique domains: " . ($customHosts->count() + $hosts->count()) . "\n\n";
        $hostsFileContent .= "# Fetch the latest version of this file: https://github.com/your-repo/hosts\n";
        $hostsFileContent .= "# Project home page: https://github.com/your-repo/hosts\n";
        $hostsFileContent .= "# Project releases: https://github.com/your-repo/hosts/releases\n\n";
        $hostsFileContent .= "# ===============================================================\n\n";

        // Add the predefined entries at the beginning
        $hostsFileContent .= "127.0.0.1 localhost\n";
        $hostsFileContent .= "127.0.0.1 localhost.localdomain\n";
        $hostsFileContent .= "127.0.0.1 local\n";
        $hostsFileContent .= "127.0.0.1 openlisto.test\n";
        $hostsFileContent .= "127.0.0.1 transparent.test\n";
        $hostsFileContent .= "127.0.0.1 transparentprofile.test\n";
        $hostsFileContent .= "127.0.0.1 presentation.test\n";
        $hostsFileContent .= "127.0.0.1 betarmagedon.test\n";
        $hostsFileContent .= "127.0.0.1 safehosts.test\n";

        // Static entries (reserved)
        $hostsFileContent .= "127.0.0.1 localhost\n";
        $hostsFileContent .= "127.0.0.1 localhost.localdomain\n";
        $hostsFileContent .= "127.0.0.1 local\n";
        $hostsFileContent .= "255.255.255.255 broadcasthost\n";
        $hostsFileContent .= "::1 localhost\n";
        $hostsFileContent .= "::1 ip6-localhost\n";
        $hostsFileContent .= "::1 ip6-loopback\n";
        $hostsFileContent .= "fe80::1%lo0 localhost\n";
        $hostsFileContent .= "ff00::0 ip6-localnet\n";
        $hostsFileContent .= "ff00::0 ip6-mcastprefix\n";
        $hostsFileContent .= "ff02::1 ip6-allnodes\n";
        $hostsFileContent .= "ff02::2 ip6-allrouters\n";
        $hostsFileContent .= "ff02::3 ip6-allhosts\n";
        $hostsFileContent .= "0.0.0.0 0.0.0.0\n\n";

        // Custom host records
        $hostsFileContent .= "# Custom host records are listed here.\n";
        foreach ($customHosts as $customHost) {
            $hostsFileContent .= "0.0.0.0 " . $customHost->host->domain . "\n";
        }
        // End of custom records
        $hostsFileContent .= "\n# End of custom host records.\n";
        // Start of the main host entries
        $hostsFileContent .= "#=====================================\n";
        $hostsFileContent .= "# Title: Hosts contributed\n";
        // Insert hosts from the `hosts` table
        foreach ($hosts as $host) {
            $hostsFileContent .= "0.0.0.0 " . $host->domain . "\n";
        }

        // Save the file
        file_put_contents($fileName, $hostsFileContent);

        $this->info("Hosts file generated successfully at: $fileName");
    }
}
