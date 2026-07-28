<?php

namespace App\Console\Commands;

use App\Services\ScmCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RelinkPortsCommand extends Command
{
    protected $signature = 'scm:relink-ports';

    protected $description = 'Menghubungkan ports.country_id ke countries.id berdasarkan kode negara atau pencocokan nama.';

    public function handle(): int
    {
        $this->info('Memulai pencocokan dan relasi ports ke countries...');

        $totalPorts = DB::table('ports')->count();
        $nullBefore = DB::table('ports')->whereNull('country_id')->count();
        $uniquePortCountryNames = DB::table('ports')
            ->whereNotNull('country_name')
            ->where('country_name', '!=', '')
            ->distinct()
            ->orderBy('country_name')
            ->pluck('country_name');

        $this->info("Total port database: {$totalPorts}");
        $this->info("country_id NULL sebelum: {$nullBefore}");
        $this->info('Nama negara unik pada ports.country_name: ' . $uniquePortCountryNames->count());

        $countryColumns = Schema::getColumnListing('countries');
        $hasOfficialName = in_array('official_name', $countryColumns, true);
        $countries = DB::table('countries')->get();
        
        $countryByCode = [];
        $countryByName = [];

        foreach ($countries as $c) {
            if (!empty($c->country_code)) {
                $countryByCode[strtoupper(trim((string) $c->country_code))] = $c;
            }

            foreach (['name', 'country_code'] as $field) {
                if (!empty($c->{$field})) {
                    $countryByName[$this->normalizeName((string) $c->{$field})] = $c;
                }
            }

            if ($hasOfficialName && !empty($c->official_name)) {
                $countryByName[$this->normalizeName((string) $c->official_name)] = $c;
            }
        }

        $ports = DB::table('ports')->get();
        $matched = 0;
        $unmatched = 0;
        $unmatchedNames = [];
        $now = now();

        foreach ($ports as $port) {
            $country = null;
            $cName = trim((string) ($port->country_name ?? ''));

            $possibleCodes = [];

            foreach (['country_code', 'iso2', 'iso_a2'] as $field) {
                if (property_exists($port, $field) && !empty($port->{$field})) {
                    $possibleCodes[] = strtoupper(trim((string) $port->{$field}));
                }
            }

            if (strlen($cName) === 2) {
                $possibleCodes[] = strtoupper($cName);
            }

            foreach (array_unique($possibleCodes) as $code) {
                if (isset($countryByCode[$code])) {
                    $country = $countryByCode[$code];
                    break;
                }
            }

            if (!$country && $cName !== '') {
                $normalized = $this->normalizeName($cName);
                
                if (isset($countryByName[$normalized])) {
                    $country = $countryByName[$normalized];
                } else {
                    $aliasName = $this->resolveAlias($normalized);
                    if ($aliasName && isset($countryByName[$aliasName])) {
                        $country = $countryByName[$aliasName];
                    }
                }
            }

            if ($country) {
                DB::table('ports')->where('id', $port->id)->update([
                    'country_id' => $country->id,
                    'updated_at' => $now,
                ]);
                $matched++;
            } else {
                $unmatched++;
                if ($cName !== '' && !in_array($cName, $unmatchedNames, true)) {
                    $unmatchedNames[] = $cName;
                }
            }
        }

        sort($unmatchedNames);
        $nullAfter = DB::table('ports')->whereNull('country_id')->count();
        $dataVersion = ScmCacheService::invalidateGlobalData();

        $this->info('=== HASIL RELINK PORTS ===');
        $this->info("Total port diperiksa: {$totalPorts}");
        $this->info("Matched: {$matched}");
        $this->info("Unmatched: {$unmatched}");
        $this->info("country_id NULL sebelum: {$nullBefore}");
        $this->info("country_id NULL sesudah: {$nullAfter}");
        $this->info("Cache global diinvalidasi. scm:data-version={$dataVersion}");
        
        if (!empty($unmatchedNames)) {
            $this->warn("Daftar nama negara yang tidak cocok: " . implode(', ', array_slice($unmatchedNames, 0, 30)) . (count($unmatchedNames) > 30 ? '...' : ''));
        }

        return self::SUCCESS;
    }

    private function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace(
            ['&', ',', '.', "'", '(', ')', '-', '_'],
            [' and ', ' ', ' ', '', ' ', ' ', ' ', ' '],
            $name
        );
        return preg_replace('/\s+/', ' ', $name);
    }

    private function resolveAlias(string $name): ?string
    {
        $aliases = [
            'united states of america' => 'united states',
            'united states america' => 'united states',
            'u s a' => 'united states',
            'usa' => 'united states',
            'us' => 'united states',
            'uk' => 'united kingdom',
            'united kingdom of great britain and northern ireland' => 'united kingdom',
            'russian federation' => 'russia',
            'viet nam' => 'vietnam',
            'lao pdr' => 'laos',
            'korea rep' => 'south korea',
            'korea republic of' => 'south korea',
            'syrian arab republic' => 'syria',
            'iran islamic republic of' => 'iran',
            'bolivia plurinational state of' => 'bolivia',
            'venezuela bolivarian republic of' => 'venezuela',
            'tanzania united republic of' => 'tanzania',
            'moldova republic of' => 'moldova',
            'brunei darussalam' => 'brunei',
            'czechia' => 'czech republic',
            'turkiye' => 'turkey',
            'bahamas the' => 'bahamas',
            'gambia the' => 'gambia',
            'egypt arab rep' => 'egypt',
            'korea rep' => 'south korea',
            'korea dem peoples rep' => 'north korea',
            'congo dem rep' => 'dr congo',
            'congo rep' => 'republic of the congo',
        ];

        return $aliases[$name] ?? null;
    }
}
