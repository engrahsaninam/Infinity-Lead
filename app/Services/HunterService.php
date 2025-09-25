<?php

namespace App\Services;

use App\Models\NameValidation;
use App\Models\Website;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class HunterService
{
    public function getDomainFromCompany($companyName)
    {
        Artisan::call('config:clear');
        $response = Http::get('https://api.hunter.io/v2/domain-search', [
            'company' => $companyName,
            'api_key' => env('HUNTER_API_KEY')
        ]);
        if ($response->successful()) {
            return $response->json()['data']['domain'] ?? null;
        }
        return null;
    }

    public function generateEmailCombinations($firstName, $lastName, $domain)
    {
        $website = Website::where('domain', $domain)->first();

        if (!$website || !in_array($website->format, Website::FORMATS)) {
            return [];
        }
        $format = $website->format;
        $domain = strtolower($domain);

        $removeCharacters = NameValidation::where('remove', 1)->pluck('symbols')->toArray();
        foreach($removeCharacters as $symbol) {
            if (strpos($firstName, $symbol) !== false) {
                $firstName = str_replace($symbol, '', $firstName);
            }
            if (strpos($lastName, $symbol) !== false) {
                $lastName = str_replace($symbol, '', $lastName);    
            }
        }

        $replaceCharacters = NameValidation::where('remove', 0)->pluck('replace', 'symbols')->toArray();
        foreach ($replaceCharacters as $symbol => $replacement) {
            $chars = preg_split('//u', $symbol, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($chars as $char) {
                if ($char !== ' ') {
                    $firstName = str_replace($char, $replacement, $firstName);
                    $lastName = str_replace($char, $replacement, $lastName);
                }
            }
        }
        $combinations = [];
        // Ensure first and last names have no spaces before generating combinations
        if (strpos($firstName, ' ') === false && strpos($lastName, ' ') === false) {
            $combinations = $this->createEmailCombinations($firstName, $lastName, $domain);
        }

        return array_unique($combinations);
        }

        /**
         * Generate all possible email combinations based on first name, last name, and domain.
         */
        private function createEmailCombinations($first, $last, $domain)
        {
        $first = strtolower($first);
        $last = strtolower($last);

        $f = substr($first, 0, 1);
        $l = substr($last, 0, 1);

        $f2 = substr($first, 0, 2);
        $f3 = substr($first, 0, 3);
        $l2 = substr($last, 0, 2);
        $l3 = substr($last, 0, 3);

        $combinations = [];

        // 1. first.last
        $combinations[] = "{$first}.{$last}@{$domain}";
        // 2. first_last
        $combinations[] = "{$first}_{$last}@{$domain}";
        // 3. first-last
        $combinations[] = "{$first}-{$last}@{$domain}";
        // 4. firstlast
        $combinations[] = "{$first}{$last}@{$domain}";
        // 5. f.last
        $combinations[] = "{$f}.{$last}@{$domain}";
        // 6. f-last
        $combinations[] = "{$f}-{$last}@{$domain}";
        // 7. flast
        $combinations[] = "{$f}{$last}@{$domain}";
        // 8. firstl
        $combinations[] = "{$first}{$l}@{$domain}";
        // 9. last.first
        $combinations[] = "{$last}.{$first}@{$domain}";
        // 10. last-first
        $combinations[] = "{$last}-{$first}@{$domain}";
        // 11. last_first
        $combinations[] = "{$last}_{$first}@{$domain}";
        // 12. lastfirst
        $combinations[] = "{$last}{$first}@{$domain}";
        // 13. last.f
        $combinations[] = "{$last}.{$f}@{$domain}";
        // 14. l.first
        $combinations[] = "{$l}.{$first}@{$domain}";
        // 15. l-first
        $combinations[] = "{$l}-{$first}@{$domain}";
        // 16. lfirst
        $combinations[] = "{$l}{$first}@{$domain}";
        // 17. first
        $combinations[] = "{$first}@{$domain}";
        // 18. last
        $combinations[] = "{$last}@{$domain}";
        // 19. f3l3
        if (strlen($first) >= 3 && strlen($last) >= 3) {
            $combinations[] = "{$f3}{$l3}@{$domain}";
        }
        // 20. f2l2
        if (strlen($first) >= 2 && strlen($last) >= 2) {
            $combinations[] = "{$f2}{$l2}@{$domain}";
        }
        // 21. f3last
        if (strlen($first) >= 3) {
            $combinations[] = "{$f3}{$last}@{$domain}";
        }
        // 22. f2last
        if (strlen($first) >= 2) {
            $combinations[] = "{$f2}{$last}@{$domain}";
        }
        // 23. fl3
        if (strlen($last) >= 3) {
            $combinations[] = "{$f}{$l3}@{$domain}";
        }
        // 24. fl2
        if (strlen($last) >= 2) {
            $combinations[] = "{$f}{$l2}@{$domain}";
        }
        // 25. fl
        $combinations[] = "{$f}.{$l}@{$domain}";
        // 26. l.f
        $combinations[] = "{$l}.{$f}@{$domain}";
        return $combinations;
    }
}
/***

first.last // first name dot last name
first_last // first name underscore last name
first-last // first name hyphen last name
firstlast // first name last name

f.last // first initial dot last name
f-last // first initial hyphen last name
flast // first initial last name

firstl // first name last initial

last.first // last name dot first name
last-first // last name hyphen first name
last_first // last name underscore first name
lastfirst // last name first name
last.f // last name dot first initial

l.first // last initial dot first name
l-first // last initial hyphen first name
lfirst // last initial first name

first // first name
last // last name

f3l3 // first 3 of first name, first 3 of last name
f2l2 // first 2 of first name, first 2 of last name
f3last // first 3 of first name, full last name
f2last // first 2 of first name, full last name
fl3 // first initial, first 3 of last name
fl2 // first initial, first 2 of last name
fl // first initial dot last name
l.f // last initial dot first initial

 */