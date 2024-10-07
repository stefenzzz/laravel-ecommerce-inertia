<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usaStates = [
            "AL" => 'Alabama',
            "AK" => 'Alaska',
            "AZ" => 'Arizona',
            "AR" => 'Arkansas',
            "CA" => 'California',
            "CO" => 'Colorado',
            "CT" => 'Connecticut',
            "DE" => 'Delaware',
            "FL" => 'Florida',
            "GA" => 'Georgia',
            "HI" => 'Hawaii',
            "ID" => 'Idaho',
            "IL" => 'Illinois',
            "IN" => 'Indiana',
            "IA" => 'Iowa',
            "KS" => 'Kansas',
            "KY" => 'Kentucky',
            "LA" => 'Louisiana',
            "ME" => 'Maine',
            "MD" => 'Maryland',
            "MA" => 'Massachusetts',
            "MI" => 'Michigan',
            "MN" => 'Minnesota',
            "MS" => 'Mississippi',
            "MO" => 'Missouri',
            "MT" => 'Montana',
            "NE" => 'Nebraska',
            "NV" => 'Nevada',
            "NH" => 'New Hampshire',
            "NJ" => 'New Jersey',
            "NM" => 'New Mexico',
            "NY" => 'New York',
            "NC" => 'North Carolina',
            "ND" => 'North Dakota',
            "OH" => 'Ohio',
            "OK" => 'Oklahoma',
            "OR" => 'Oregon',
            "PA" => 'Pennsylvania',
            "RI" => 'Rhode Island',
            "SC" => 'South Carolina',
            "SD" => 'South Dakota',
            "TN" => 'Tennessee',
            "TX" => 'Texas',
            "UT" => 'Utah',
            "VT" => 'Vermont',
            "VA" => 'Virginia',
            "WA" => 'Washington',
            "WV" => 'West Virginia',
            "WI" => 'Wisconsin',
            "WY" => 'Wyoming',
            "DC" => 'District of Columbia', // D.C. is a federal district
        ];

        $countries = [
            ['code' => 'usa', 'name' => 'United States', 'states' => json_encode($usaStates)],
            ['code' => 'can', 'name' => 'Canada', 'states' => null],
            ['code' => 'gbr', 'name' => 'United Kingdom', 'states' => null],
            ['code' => 'aus', 'name' => 'Australia', 'states' => null],
            ['code' => 'ger', 'name' => 'Germany', 'states' => null],
            ['code' => 'fra', 'name' => 'France', 'states' => null],
            ['code' => 'ita', 'name' => 'Italy', 'states' => null],
            ['code' => 'esp', 'name' => 'Spain', 'states' => null],
            ['code' => 'ind', 'name' => 'India', 'states' => null],
            ['code' => 'bra', 'name' => 'Brazil', 'states' => null],
            ['code' => 'jpn', 'name' => 'Japan', 'states' => null],
            ['code' => 'chn', 'name' => 'China', 'states' => null],
            ['code' => 'mex', 'name' => 'Mexico', 'states' => null],
            ['code' => 'rus', 'name' => 'Russia', 'states' => null],
            ['code' => 'nld', 'name' => 'Netherlands', 'states' => null],
            ['code' => 'bel', 'name' => 'Belgium', 'states' => null],
            ['code' => 'swe', 'name' => 'Sweden', 'states' => null],
            ['code' => 'nor', 'name' => 'Norway', 'states' => null],
            ['code' => 'fin', 'name' => 'Finland', 'states' => null],
            ['code' => 'den', 'name' => 'Denmark', 'states' => null],
            ['code' => 'che', 'name' => 'Switzerland', 'states' => null],
            ['code' => 'aut', 'name' => 'Austria', 'states' => null],
            ['code' => 'irl', 'name' => 'Ireland', 'states' => null],
            ['code' => 'prt', 'name' => 'Portugal', 'states' => null],
            ['code' => 'pol', 'name' => 'Poland', 'states' => null],
            ['code' => 'tur', 'name' => 'Turkey', 'states' => null],
            ['code' => 'sau', 'name' => 'Saudi Arabia', 'states' => null],
            ['code' => 'are', 'name' => 'United Arab Emirates', 'states' => null],
            ['code' => 'sgp', 'name' => 'Singapore', 'states' => null],
            ['code' => 'mys', 'name' => 'Malaysia', 'states' => null],
            ['code' => 'tha', 'name' => 'Thailand', 'states' => null],
            ['code' => 'kor', 'name' => 'South Korea', 'states' => null],
            ['code' => 'vnm', 'name' => 'Vietnam', 'states' => null],
            ['code' => 'idn', 'name' => 'Indonesia', 'states' => null],
            ['code' => 'phl', 'name' => 'Philippines', 'states' => null],
            ['code' => 'egy', 'name' => 'Egypt', 'states' => null],
            ['code' => 'zaf', 'name' => 'South Africa', 'states' => null],
            ['code' => 'ken', 'name' => 'Kenya', 'states' => null],
            ['code' => 'nga', 'name' => 'Nigeria', 'states' => null],
            ['code' => 'arg', 'name' => 'Argentina', 'states' => null],
            ['code' => 'chl', 'name' => 'Chile', 'states' => null],
            ['code' => 'col', 'name' => 'Colombia', 'states' => null],
            ['code' => 'per', 'name' => 'Peru', 'states' => null],
            ['code' => 'ury', 'name' => 'Uruguay', 'states' => null],
            ['code' => 'isr', 'name' => 'Israel', 'states' => null],
            ['code' => 'pak', 'name' => 'Pakistan', 'states' => null],
            ['code' => 'bgd', 'name' => 'Bangladesh', 'states' => null],
            ['code' => 'nzl', 'name' => 'New Zealand', 'states' => null],
            ['code' => 'grc', 'name' => 'Greece', 'states' => null],
            ['code' => 'cze', 'name' => 'Czech Republic', 'states' => null],
            ['code' => 'hun', 'name' => 'Hungary', 'states' => null],
        ];

                
        Country::insert($countries);      
    }
}
