<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Host;
use App\Models\UserCustomList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class HostsDownloadController extends Controller
{
    public function downloadAdmin()
    {
        $fileName = 'custom_hosts.txt';
        $currentDate = Carbon::now()->toDateTimeString();

        $customHosts = UserCustomList::with('host')
            ->where('user_id', 1)
            ->get();

        $hosts = Host::all();

        $hostsFileContent = "# This hosts file is a merged collection of hosts from reputable sources,\n";
        $hostsFileContent .= "# with a dash of crowd sourcing via GitHub\n\n";
        $hostsFileContent .= "# Date: $currentDate (UTC)\n";
        $hostsFileContent .= "# Number of unique domains: " . ($customHosts->count() + $hosts->count()) . "\n\n";

        // Predefined entries
        $hostsFileContent .= "127.0.0.1 localhost\n";
        $hostsFileContent .= "127.0.0.1 localhost.localdomain\n";
        $hostsFileContent .= "127.0.0.1 local\n";
        $hostsFileContent .= "255.255.255.255 broadcasthost\n";
        $hostsFileContent .= "::1 localhost\n\n";

        // Custom host records
        $hostsFileContent .= "# Custom host records\n";
        foreach ($customHosts as $customHost) {
            $hostsFileContent .= "0.0.0.0 " . $customHost->host->domain . "\n";
        }
        $hostsFileContent .= "\n# End of custom host records\n\n";

        // Hosts table
        foreach ($hosts as $host) {
            $hostsFileContent .= "0.0.0.0 " . $host->domain . "\n";
        }

        // Salvează în storage temporar
        Storage::disk('local')->put($fileName, $hostsFileContent);

        // Trimite direct pentru download
        return response()->download(storage_path("app/{$fileName}"))->deleteFileAfterSend(true);
    }
    public function generateGuest(Request $request)
    {
        $fileName = trim($request->input('fileName', 'custom_hosts.txt'));
        // sanitize filename (very basic)
        $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
        if (empty($fileName)) {
            $fileName = 'custom_hosts.txt';
        }

        $includeDefaults = $request->has('includeDefaults');
        $customHostsText = (string) $request->input('customHosts', '');
        $lines = preg_split("/\r\n|\n|\r/", $customHostsText);

        $content = "# SafeHost generated hosts file\n";
        $content .= "# Generated: " . now()->toDateTimeString() . " (UTC)\n\n";

        if ($includeDefaults) {
            $content .= "127.0.0.1 localhost\n";
            $content .= "127.0.0.1 localhost.localdomain\n";
            $content .= "127.0.0.1 local\n";
            $content .= "255.255.255.255 broadcasthost\n";
            $content .= "::1 localhost\n\n";
        }

        if (!empty(trim($customHostsText))) {
            $content .= "# Custom host records (temporary, user provided)\n";
            foreach ($lines as $line) {
                $domain = trim($line);
                if ($domain === '') {
                    continue;
                }
                // optional: simple validation of domain-like pattern
                $domain = preg_replace('/\s+/', '', $domain);
                $content .= "0.0.0.0 {$domain}\n";
            }
            $content .= "\n";
        }

        // store old inputs to repopulate if needed
        $oldCustomHosts = $customHostsText;
        return view('welcome')->with([
            'hostsFileContent' => $content,
            'fileName' => $fileName,
            'includeDefaults' => $includeDefaults,
            'oldCustomHosts' => $oldCustomHosts,
        ]);
    }

    public function downloadGuest(Request $request)
    {
        $content = html_entity_decode($request->input('hostsFileContent', ''), ENT_QUOTES | ENT_HTML5);
        $fileName = trim($request->input('fileName', 'custom_hosts.txt'));
        $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
        if (empty($fileName)) {
            $fileName = 'custom_hosts.txt';
        }

        // record in session for post-download instructions
        session()->flash('downloaded', $fileName);

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

}
