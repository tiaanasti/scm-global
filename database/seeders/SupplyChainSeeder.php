<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupplyChainSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@supplyrisk.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $indonesiaId = DB::table('countries')->insertGetId([
            'country_code' => 'ID',
            'name' => 'Indonesia',
            'capital' => 'Jakarta',
            'region' => 'Asia Tenggara',
            'currency_code' => 'IDR',
            'currency_name' => 'Rupiah',
            'language' => 'Bahasa Indonesia',
            'latitude' => -6.2000000,
            'longitude' => 106.8166660,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $chinaId = DB::table('countries')->insertGetId([
            'country_code' => 'CN',
            'name' => 'China',
            'capital' => 'Beijing',
            'region' => 'Asia Timur',
            'currency_code' => 'CNY',
            'currency_name' => 'Yuan',
            'language' => 'Mandarin',
            'latitude' => 39.9042000,
            'longitude' => 116.4074000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $germanyId = DB::table('countries')->insertGetId([
            'country_code' => 'DE',
            'name' => 'Germany',
            'capital' => 'Berlin',
            'region' => 'Eropa',
            'currency_code' => 'EUR',
            'currency_name' => 'Euro',
            'language' => 'German',
            'latitude' => 52.5200000,
            'longitude' => 13.4050000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $australiaId = DB::table('countries')->insertGetId([
            'country_code' => 'AU',
            'name' => 'Australia',
            'capital' => 'Canberra',
            'region' => 'Oceania',
            'currency_code' => 'AUD',
            'currency_name' => 'Australian Dollar',
            'language' => 'English',
            'latitude' => -35.2809000,
            'longitude' => 149.1300000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('economic_indicators')->insert([
            [
                'country_id' => $indonesiaId,
                'year' => 2024,
                'gdp' => 1370000000000,
                'inflation_rate' => 2.8,
                'population' => 278680000,
                'exports' => 292000000000,
                'imports' => 237000000000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'year' => 2024,
                'gdp' => 17800000000000,
                'inflation_rate' => 1.1,
                'population' => 1412000000,
                'exports' => 3590000000000,
                'imports' => 2710000000000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'year' => 2024,
                'gdp' => 4520000000000,
                'inflation_rate' => 2.2,
                'population' => 84600000,
                'exports' => 1680000000000,
                'imports' => 1480000000000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'year' => 2024,
                'gdp' => 1690000000000,
                'inflation_rate' => 3.6,
                'population' => 26700000,
                'exports' => 420000000000,
                'imports' => 360000000000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('weather_reports')->insert([
            [
                'country_id' => $indonesiaId,
                'temperature' => 29,
                'rainfall' => 12.4,
                'wind_speed' => 14,
                'weather_status' => 'Hujan Ringan',
                'weather_risk_score' => 45,
                'reported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'temperature' => 22,
                'rainfall' => 30,
                'wind_speed' => 18,
                'weather_status' => 'Hujan Sedang',
                'weather_risk_score' => 60,
                'reported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'temperature' => 18,
                'rainfall' => 5,
                'wind_speed' => 10,
                'weather_status' => 'Berawan',
                'weather_risk_score' => 25,
                'reported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'temperature' => 22,
                'rainfall' => 8,
                'wind_speed' => 12,
                'weather_status' => 'Cerah',
                'weather_risk_score' => 30,
                'reported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('currency_rates')->insert([
            [
                'country_id' => $indonesiaId,
                'base_currency' => 'USD',
                'target_currency' => 'IDR',
                'exchange_rate' => 16245,
                'change_percentage' => 0.68,
                'currency_risk_score' => 38,
                'rate_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'base_currency' => 'USD',
                'target_currency' => 'CNY',
                'exchange_rate' => 7.21,
                'change_percentage' => 0.41,
                'currency_risk_score' => 50,
                'rate_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'base_currency' => 'USD',
                'target_currency' => 'EUR',
                'exchange_rate' => 0.91,
                'change_percentage' => -0.30,
                'currency_risk_score' => 18,
                'rate_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'base_currency' => 'USD',
                'target_currency' => 'AUD',
                'exchange_rate' => 1.52,
                'change_percentage' => 0.20,
                'currency_risk_score' => 22,
                'rate_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('risk_scores')->insert([
            [
                'country_id' => $indonesiaId,
                'weather_score' => 45,
                'inflation_score' => 20,
                'currency_score' => 38,
                'news_score' => 25,
                'total_score' => 42,
                'risk_level' => 'Risiko Sedang',
                'recommendation' => 'Pantau perubahan cuaca di sekitar pelabuhan utama dan perhatikan fluktuasi kurs yang dapat memengaruhi biaya impor.',
                'score_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'weather_score' => 60,
                'inflation_score' => 18,
                'currency_score' => 50,
                'news_score' => 70,
                'total_score' => 65,
                'risk_level' => 'Risiko Tinggi',
                'recommendation' => 'Risiko meningkat karena kombinasi berita negatif, kepadatan pelabuhan, dan potensi gangguan cuaca.',
                'score_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'weather_score' => 25,
                'inflation_score' => 22,
                'currency_score' => 18,
                'news_score' => 20,
                'total_score' => 28,
                'risk_level' => 'Risiko Rendah',
                'recommendation' => 'Kondisi relatif aman untuk perencanaan impor karena indikator ekonomi dan kurs cukup stabil.',
                'score_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'weather_score' => 30,
                'inflation_score' => 36,
                'currency_score' => 22,
                'news_score' => 24,
                'total_score' => 35,
                'risk_level' => 'Risiko Sedang',
                'recommendation' => 'Negara ini cukup aman, tetapi tetap perlu memantau inflasi dan perubahan kurs.',
                'score_date' => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('ports')->insert([
            [
                'country_id' => $indonesiaId,
                'name' => 'Tanjung Priok',
                'city' => 'Jakarta',
                'country_name' => 'Indonesia',
                'latitude' => -6.1021,
                'longitude' => 106.8850,
                'status' => 'Aman',
                'port_risk_score' => 35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'name' => 'Shanghai Port',
                'city' => 'Shanghai',
                'country_name' => 'China',
                'latitude' => 31.2304,
                'longitude' => 121.4737,
                'status' => 'Waspada',
                'port_risk_score' => 75,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'name' => 'Port of Hamburg',
                'city' => 'Hamburg',
                'country_name' => 'Germany',
                'latitude' => 53.5511,
                'longitude' => 9.9937,
                'status' => 'Aman',
                'port_risk_score' => 28,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'name' => 'Port Botany',
                'city' => 'Sydney',
                'country_name' => 'Australia',
                'latitude' => -33.9667,
                'longitude' => 151.2250,
                'status' => 'Siaga',
                'port_risk_score' => 48,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('news_cache')->insert([
            [
                'country_id' => $indonesiaId,
                'title' => 'Ekspor Indonesia tumbuh stabil pada kuartal ini',
                'description' => 'Aktivitas ekspor menunjukkan tren positif meskipun biaya logistik global masih perlu diperhatikan.',
                'source' => 'Demo News',
                'url' => '#',
                'category' => 'Perdagangan',
                'sentiment' => 'Positive',
                'positive_score' => 3,
                'negative_score' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $chinaId,
                'title' => 'Keterlambatan pengiriman meningkat di beberapa pelabuhan Asia',
                'description' => 'Gangguan cuaca dan kepadatan pelabuhan meningkatkan risiko keterlambatan rantai pasok.',
                'source' => 'Demo News',
                'url' => '#',
                'category' => 'Logistik',
                'sentiment' => 'Negative',
                'positive_score' => 1,
                'negative_score' => 4,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $germanyId,
                'title' => 'Stabilitas ekonomi Jerman mendukung aktivitas impor',
                'description' => 'Kondisi ekonomi dan kurs yang stabil membantu menekan risiko biaya impor.',
                'source' => 'Demo News',
                'url' => '#',
                'category' => 'Ekonomi',
                'sentiment' => 'Positive',
                'positive_score' => 4,
                'negative_score' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $australiaId,
                'title' => 'Australia menjaga stabilitas jalur ekspor komoditas',
                'description' => 'Cuaca relatif aman dan kegiatan pelabuhan berjalan normal, namun inflasi tetap perlu dipantau.',
                'source' => 'Demo News',
                'url' => '#',
                'category' => 'Ekonomi',
                'sentiment' => 'Neutral',
                'positive_score' => 2,
                'negative_score' => 2,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('watchlists')->insert([
            [
                'user_id' => $adminId,
                'country_id' => $indonesiaId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $adminId,
                'country_id' => $chinaId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $adminId,
                'country_id' => $germanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $adminId,
                'country_id' => $australiaId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('articles')->insert([
            [
                'user_id' => $adminId,
                'title' => 'Analisis Risiko Rantai Pasok Asia',
                'content' => 'Artikel ini membahas peningkatan risiko logistik di kawasan Asia akibat cuaca, kurs, dan kepadatan pelabuhan.',
                'category' => 'Analisis',
                'status' => 'Published',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $adminId,
                'title' => 'Dampak Kurs terhadap Biaya Impor',
                'content' => 'Fluktuasi nilai tukar dapat memengaruhi biaya impor dan keputusan pembelian internasional.',
                'category' => 'Ekonomi',
                'status' => 'Draft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('positive_words')->insert([
            ['word' => 'growth', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'increase', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'profit', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'stable', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'improve', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'recovery', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'export', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'safe', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('negative_words')->insert([
            ['word' => 'war', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'crisis', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'inflation', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'delay', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'disaster', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'conflict', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'disruption', 'created_at' => $now, 'updated_at' => $now],
            ['word' => 'shortage', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('api_logs')->insert([
            [
                'api_name' => 'Open-Meteo API',
                'endpoint' => 'weather forecast',
                'status' => 'Success',
                'message' => 'Demo weather data inserted successfully.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'api_name' => 'World Bank API',
                'endpoint' => 'economic indicators',
                'status' => 'Success',
                'message' => 'Demo economic data inserted successfully.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}