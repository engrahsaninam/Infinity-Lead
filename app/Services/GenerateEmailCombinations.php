<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateEmailCombinations
{
    protected array $finalSuffixList = [
        'dr','mr','ms','jr','sr','ceo','cfo','cto','cmo','cio','cpa','mba','phd','prof','md','do','dds','dvm','esq','jd','pmp','cfa','rn','np','ph'
    ];
    protected array $globalSuffixesToExclude = [
        "CEng", "jr", "sr", "DipPFS", "BA", "PG Dip", "Assoc Dir", "CAO", "CEO", "CDO", "CFO", "Chair", "CHRO", "CIO", "CLO", "CMO", "ADV", "CPO", "CSO", "CTO", "Dir", "ED", "EVP", "MP", "Pres", "SGT", "SUPR", "Supe", "VC", "VP", "VP of TA", "VP of TM", "Advisor", "AE", "AMB", "Acct Mgr", "EAM", "HRM", "AM", "Sr Mgr", "Ter Mgr", "Admin Asst", "Asst", "Exec Asst", "EA", "VA", "AOCN", "AA", "AASA", "AD", "AVP", "assistant", "Aero Eng", "CE", "C/E", "Chem Engr", "Cloud Ops Eng", "CNE", "Comp Engr", "Electr Eng", "Environ Engr", "Eng", "Engr", "Jr Engr", "Manuf Eng", "Mech Engr", "Prof Engr", "Sr. AE", "Struct Eng", "SWE", "UI Eng", "Software Developer", "CAPA", "BE Developer", "Dev", "FE Developer", "JS Developer", "Sales", "BDM", "BDR", "Sales Rep", "SDR", "Design", "Art Dir", "Creative Dir", "UI Designer", "UX Designer", "Professional", "AAMS", "Admin", "CCNS", "Acct", "Agt", "Anlst", "APR", "AAPR", "Assoc", "Assoc Prof", "ATTD", "CFP", "CHANC", "Coord", "CPA", "CSM", "CSR", "CNA", "DEVL", "DSGN", "ELEC", "FAC", "GC", "HRBP", "INSP", "INSTR", "INT", "IT", "Junior", "LEC", "LIBR", "MACH", "MECH", "Mkg", "Mkt", "Mktg", "Off", "OPER", "PRIN", "PRO", "Prof", "Prog", "Rep", "ROR Dev", "QA Analyst", "Sales Assoc", "SM Spec", "Specl", "Tech", "TRNE", "TRNR", "UX Des", "Judge", "ALJ", "Arb", "Assemb", "AG", "Att'y Gen", "C.B.", "C.J.", "Mag", "Med", "CNP", "Medical", "NURSE", "APRN", "NP", "Doctor", "Dr.", "RN", "DO", "RPh", "Phar", "Pharm", "DPM", "DCH", "DDS", "DMD", "FACD", "FAGD", "MDS", "MSD", "RDA", "RDH", "PH", "DrNP", "MD", "APRN-CNM", "ACNP-BC", "APRN-ANP", "APRN-AGACNP", "FAAN", "FAEN", "RN-BC", "OD", "D.O.", "Physician", "Dentist", "Lawyer", "Software Engineer", "Doctor of Nursing Practice", "Surgeon", "Medical Assistant", "Director", "Manager", "Architect", "Chief Engineer", "Systems Engineer", "Product Manager", "MEng MIChemE", "MIChemE", "MEng", "PMP", "Jr.", "MSc.", "Mr", "MBA", "Cert CIIMP", "MCIPPdip", "MCIPS", "MRTPI", "DipFA", "DipFA Cert MA", "CIPD", "CMgr MCMI", "MCMI", "CMgr", "Bsc Cips", "Cips", "Bsc", "Miet", "Cltd", "Bsc Cdcp", "Cdcp", "Head At G4s", "Cmgr McMi Bsc Cdcp", "Cert Cii", "Dip Cii", "Senior Manager • Director Cissp", "McGi Sac Dip Rospa Dip Adi", "Dipm McIm Chartered Marketer", "Ceng Miet Cmareng Mimarest", "Acii Chartered Insurer", "Assoc. Cipd", "Health Coach", "McIob Ciwfm Bsc", "Cert Cii Iap", "Ba Dip Cii", "Cert Cii", "I-Eng Miet", "Dip Cii", "CFa"
    ];
    protected array $complexPrefixes = [
        'van der', 'de la', 'de los', 'von', 'de', 'van', 'le', 'di', 'da', 'del', 'du', 'des'
    ];

    protected string $finalSuffixPattern;

    public function __construct()
    {
        $re = implode('|', array_map('preg_quote', $this->finalSuffixList));
        $this->finalSuffixPattern = '/\b(?:' . $re . ')\b/i';
    }

    public function generateEmailCombinations($firstName, $lastName, $domain, $company)
    {
        if (!$domain || trim($domain) === '') {
            $domain = DB::table('websites')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($company)])
                ->value('domain');
        }

        if (!$domain || trim($domain) === '') {
            Log::warning("Skipping record: missing domain for {$company}");
            return [];
        }

        $firstNameCleaned = $this->cleanSuffixes($firstName);
        $lastNameCleaned = $this->cleanSuffixes($lastName);

        if ($firstNameCleaned === '' || $lastNameCleaned === '') {
            Log::info("Skipping {$company} due to empty names after cleaning");
            return [];
        }

        $firstNameParts = array_filter(explode(' ', trim($firstNameCleaned)));

        $formats = DB::table('websites')
            ->whereRaw('LOWER(domain) = ?', [mb_strtolower($domain)])
            ->pluck('format')
            ->unique()
            ->values()
            ->toArray();

        if (empty($formats)) {
            $formats = [
                'firstname.lastname',
                'firstnamelastname',
                'firstname',
                'lastname',
                'firstinitiallastname',
                'firstname.lastinitial',
                'firstinitial.lastname',
                'lastname.firstname',
                'firstname_lastname',
                'firstname-lastname',
                'lastname_firstname',
                'lastname-firstname',
                'lastnamefirstname',
                'lastname.firstinitial',
                'lastnamefirstinitial',
                'first3last3',
                'first2last2',
                'first3lastname',
                'first2lastname',
                'firstinitiallast3',
                'firstinitiallast2',
                'lastinitialfirstinitial',
                'lastinitial.firstinitial'
            ];
        }
        return $this->createEmailsByFormat($firstNameParts, $lastNameCleaned, $formats, strtolower($domain));
    }

    private function replaceAccentedCharacters($text)
    {
        if (!is_string($text) || $text === '') return '';
        
        $replacements = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ā' => 'a', 'ă' => 'a', 'ą' => 'a',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ă' => 'A', 'Ą' => 'A',
            'æ' => 'ae', 'Æ' => 'AE',
            'ç' => 'c', 'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'č' => 'c',
            'Ç' => 'C', 'Ć' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Č' => 'C',
            'ď' => 'd', 'đ' => 'd', 'Ď' => 'D', 'Đ' => 'D',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ę' => 'E', 'Ě' => 'E',
            'ğ' => 'g', 'ĝ' => 'g', 'ġ' => 'g', 'ģ' => 'g',
            'Ğ' => 'G', 'Ĝ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
            'ĥ' => 'h', 'ħ' => 'h', 'Ĥ' => 'H', 'Ħ' => 'H',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'I' => 'I',
            'ĳ' => 'ij', 'Ĳ' => 'IJ',
            'ĵ' => 'j', 'Ĵ' => 'J',
            'ķ' => 'k', 'Ķ' => 'K',
            'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l',
            'Ĺ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L', 'Ł' => 'L',
            'ñ' => 'n', 'ń' => 'n', 'ņ' => 'n', 'ň' => 'n', 'ŋ' => 'n',
            'Ñ' => 'N', 'Ń' => 'N', 'Ņ' => 'N', 'Ň' => 'N', 'Ŋ' => 'N',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O',
            'œ' => 'oe', 'Œ' => 'OE',
            'ŕ' => 'r', 'ŗ' => 'r', 'ř' => 'r',
            'Ŕ' => 'R', 'Ŗ' => 'R', 'Ř' => 'R',
            'ś' => 's', 'ŝ' => 's', 'ş' => 's', 'š' => 's',
            'Ś' => 'S', 'Ŝ' => 'S', 'Ş' => 'S', 'Š' => 'S',
            'ţ' => 't', 'ť' => 't', 'ŧ' => 't',
            'Ţ' => 'T', 'Ť' => 'T', 'Ŧ' => 'T',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ũ' => 'U', 'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ų' => 'U',
            'ŵ' => 'w', 'Ŵ' => 'W',
            'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y',
            'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y',
            'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
            'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z',
        ];

        return strtr($text, $replacements);
    }

    private function cleanSuffixes($nameStr)
    {
        if (!is_string($nameStr) || trim($nameStr) === '') return '';
        $nameStr = $this->replaceAccentedCharacters($nameStr);

        $allSuffixesToRemove = array_flip(array_map('mb_strtolower', $this->globalSuffixesToExclude));

        $complexPrefixes = $this->complexPrefixes;
        usort($complexPrefixes, fn($a, $b) => strlen($b) - strlen($a));

        $nameLower = mb_strtolower(trim($nameStr));
        $nameParts = preg_split('/\s+/', $nameLower);

        $cleanedParts = [];
        $i = 0;
        
        while ($i < count($nameParts)) {
            $matchedPrefix = false;
            foreach ($complexPrefixes as $cp) {
                $cpParts = explode(' ', $cp);
                if (count($cpParts) > 1 && $i + count($cpParts) <= count($nameParts)) {
                    $currentSegment = implode(' ', array_slice($nameParts, $i, count($cpParts)));
                    if ($currentSegment === $cp && !isset($allSuffixesToRemove[$cp])) {
                        $cleanedParts = array_merge($cleanedParts, $cpParts);
                        $i += count($cpParts);
                        $matchedPrefix = true;
                        break;
                    }
                }
            }
            if ($matchedPrefix) continue;

            $part = $nameParts[$i];
            $partCleanedPunc = preg_replace('/[^a-zA-Z0-9]/', '', $part);
            if (!isset($allSuffixesToRemove[$partCleanedPunc])) $cleanedParts[] = $part;
            $i++;
        }
        return trim(implode(' ', $cleanedParts));
    }

    private function generateLastNameVariations($lastNameCleaned)
    {
        $variations = [];
        if (!is_string($lastNameCleaned) || trim($lastNameCleaned) === '') return [];
        $lastNameNormalized = $this->replaceAccentedCharacters($lastNameCleaned);
        $lastNameLower = preg_replace('/^[^\w]+|[^\w]+$/', '', str_replace("'", "", mb_strtolower($lastNameNormalized)));
        $parts = array_filter(preg_split('/[^a-z0-9]+/', $lastNameLower));

        if (empty($parts)) return [];

        $fullCombined = implode('', $parts);
        $variations[] = $fullCombined;

        $mainLastNamePart = end($parts);
        $variations[] = $mainLastNamePart;

        if (count($parts) > 1) {
            $variations[] = implode('.', $parts);
            $variations[] = implode('-', $parts);
        }
        return array_unique($variations);
    }

    private function createEmailsByFormat(array $firstNames, string $lastName, array $formats, string $domain)
    {
        $emails = [];
        $firstNamesLower = array_map('mb_strtolower', array_filter($firstNames));
        if (empty($firstNamesLower)) return [];
        $lastNameVariations = $this->generateLastNameVariations($lastName);
        if (empty($lastNameVariations)) return [];

        $commonPrefixesToExclude = ['van', 'de', 'der', 'le', 'di', 'da', 'del', 'du', 'des', 'von'];

        foreach ($firstNamesLower as $fn) {
            $fInitial = $fn[0] ?? '';
            $firstThree = strlen($fn) >= 3 ? substr($fn, 0, 3) : $fn;
            $firstTwo = strlen($fn) >= 2 ? substr($fn, 0, 2) : $fn;

            foreach ($lastNameVariations as $lastVariant) {
                if (!$lastVariant) continue;

                $lastInitial = $lastVariant[0] ?? '';
                $lastThree = strlen($lastVariant) >= 3 ? substr($lastVariant, 0, 3) : $lastVariant;
                $lastTwo = strlen($lastVariant) >= 2 ? substr($lastVariant, 0, 2) : $lastVariant;

                foreach ($formats as $emailFormat) {
                    $emailFormat = mb_strtolower($emailFormat);
                    $emailLocalPart = '';
                    switch ($emailFormat) {
                        case 'first.last':
                            $emailLocalPart = "{$fn}.{$lastVariant}";
                            break;
                        case 'first-last':
                            $emailLocalPart = "{$fn}-{$lastVariant}";
                            break;
                        case 'firstlast':
                            $emailLocalPart = "{$fn}{$lastVariant}";
                            break;
                        case 'f.last':
                            if ($fInitial) $emailLocalPart = "{$fInitial}.{$lastVariant}";
                            break;
                        case 'f-last':
                            if ($fInitial) $emailLocalPart = "{$fInitial}-{$lastVariant}";
                            break;
                        case 'flast':
                            if ($fInitial) $emailLocalPart = "{$fInitial}{$lastVariant}";
                            break;
                        case 'last.first':
                            $emailLocalPart = "{$lastVariant}.{$fn}";
                            break;
                        case 'last-first':
                            $emailLocalPart = "{$lastVariant}-{$fn}";
                            break;
                        case 'lastfirst':
                            $emailLocalPart = "{$lastVariant}{$fn}";
                            break;
                        case 'l.first':
                            if ($lastInitial) $emailLocalPart = "{$lastInitial}.{$fn}";
                            break;
                        case 'l-first':
                            if ($lastInitial) $emailLocalPart = "{$lastInitial}-{$fn}";
                            break;
                        case 'lfirst':
                            if ($lastInitial) $emailLocalPart = "{$lastInitial}{$fn}";
                            break;
                        case 'first':
                            $emailLocalPart = $fn;
                            break;
                        case 'last':
                            $emailLocalPart = $lastVariant;
                            break;
                        case 'firstname.lastname':
                            $emailLocalPart = "{$fn}.{$lastVariant}";
                            break;
                        case 'firstnamelastname':
                            $emailLocalPart = "{$fn}{$lastVariant}";
                            break;
                        case 'firstname':
                            $emailLocalPart = $fn;
                            break;
                        case 'lastname':
                            $emailLocalPart = $lastVariant;
                            break;
                        case 'firstinitiallastname':
                            if ($fInitial) $emailLocalPart = "{$fInitial}{$lastVariant}";
                            break;
                        case 'firstname.lastinitial':
                            if ($lastInitial) $emailLocalPart = "{$fn}.{$lastInitial}";
                            break;
                        case 'firstinitial.lastname':
                            if ($fInitial) $emailLocalPart = "{$fInitial}.{$lastVariant}";
                            break;
                        case 'lastname.firstname':
                            $emailLocalPart = "{$lastVariant}.{$fn}";
                            break;
                        case 'firstname_lastname':
                            $emailLocalPart = "{$fn}_{$lastVariant}";
                            break;
                        case 'firstname-lastname':
                            $emailLocalPart = "{$fn}-{$lastVariant}";
                            break;
                        case 'lastname_firstname':
                            $emailLocalPart = "{$lastVariant}_{$fn}";
                            break;
                        case 'lastname-firstname':
                            $emailLocalPart = "{$lastVariant}-{$fn}";
                            break;
                        case 'lastnamefirstname':
                            if ($lastVariant && $fn) $emailLocalPart = "{$lastVariant}{$fn}";
                            break;
                        case 'lastname.firstinitial':
                            if ($fInitial) $emailLocalPart = "{$lastVariant}.{$fInitial}";
                            break;
                        case 'lastnamefirstinitial':
                            if ($fInitial) $emailLocalPart = "{$lastVariant}{$fInitial}";
                            break;
                        case 'first3last3':
                            if ($firstThree && $lastThree) $emailLocalPart = "{$firstThree}{$lastThree}";
                            break;
                        case 'first2last2':
                            if ($firstTwo && $lastTwo) $emailLocalPart = "{$firstTwo}{$lastTwo}";
                            break;
                        case 'first3lastname':
                            if ($firstThree) $emailLocalPart = "{$firstThree}{$lastVariant}";
                            break;
                        case 'first2lastname':
                            if ($firstTwo) $emailLocalPart = "{$firstTwo}{$lastVariant}";
                            break;
                        case 'firstinitiallast3':
                            if ($fInitial && $lastThree) $emailLocalPart = "{$fInitial}{$lastThree}";
                            break;
                        case 'firstinitiallast2':
                            if ($fInitial && $lastTwo) $emailLocalPart = "{$fInitial}{$lastTwo}";
                            break;
                        case 'lastinitialfirstinitial':
                            if ($lastInitial && $fInitial) $emailLocalPart = "{$lastInitial}{$fInitial}";
                            break;
                        case 'lastinitial.firstinitial':
                            if ($lastInitial && $fInitial) $emailLocalPart = "{$lastInitial}.{$fInitial}";
                            break;
                    }

                    if ($emailLocalPart) {
                        if (preg_match($this->finalSuffixPattern, $emailLocalPart)) continue;
                        if (in_array(mb_strtolower($emailLocalPart), $commonPrefixesToExclude) || trim($emailLocalPart) === '') continue;
                        $email = mb_strtolower("{$emailLocalPart}@{$domain}");
                        if ($this->isValidEmail($email)) $emails[$email] = true;
                    }
                }
            }
        }
        return array_keys($emails);
    }

    private function isValidEmail($email)
    {
        $pattern = '/^(?!.*\.\.)[a-zA-Z0-9._%+-]+(?<!\.)@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($pattern, $email) === 1;
    }
}