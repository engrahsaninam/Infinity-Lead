<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NameValidation;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class NameValidationController extends Controller
{
    use CommonTraits;
    public function fetch()
    {
        return response()->json(NameValidation::orderBy('symbols','ASC')->get());
    }
    public function storeRemove(Request $request)
    {
        $request->validate([
            'symbols' => 'required|string',
        ]);
        
        $symbol = $request->symbols;
        $existing = NameValidation::where('remove', true)
        ->whereRaw('LOWER(symbols) LIKE ?', ["%$symbol%"])
        ->get()
        ->filter(function ($item) use ($symbol) {
            $symbols = preg_split('/\s+/', strtolower($item->symbols));
            return in_array(strtolower($symbol), $symbols);
        })->first();
        if ($existing) {
            return $this->sendError('This symbol already exists in remove list.',421);
        }
        $request->merge(['remove'=>true]);
        NameValidation::create($request->only('symbols', 'remove'));
        return $this->sendSuccess('Symbol saved.');
    }
    public function storeReplace(Request $request)
    {
        $request->validate([
            'symbols' => 'required|string',
            'remove' => 'required|boolean',
            'replace' => 'nullable|string',
        ]);
        $entry = NameValidation::create($request->only('symbols', 'remove', 'replace'));
        return response()->json(['message' => 'Symbol saved.', 'data' => $entry]);
    }
    

    public function delete(Request $request)
    {
        NameValidation::findOrFail($request->id)->delete();
        return response()->json(['message' => 'Symbol deleted.']);
    }
    public function importReplace(Request $request)
    {
        NameValidation::where('remove',false)->delete();
        $mappings = [
            'à á â ã ä å ā ă ą' => ['remove' => false, 'replace' => 'a'],
            'À Á Â Ã Ä Å Ā Ă Ą' => ['remove' => false, 'replace' => 'A'],
            'æ'                => ['remove' => false, 'replace' => 'ae'],
            'Æ'                => ['remove' => false, 'replace' => 'AE'],
            'ç ć ĉ ċ č'        => ['remove' => false, 'replace' => 'c'],
            'Ç Ć Ĉ Ċ Č'        => ['remove' => false, 'replace' => 'C'],
            'ď đ'              => ['remove' => false, 'replace' => 'd'],
            'Ď Đ'              => ['remove' => false, 'replace' => 'D'],
            'è é ê ë ē ĕ ė ę ě' => ['remove' => false, 'replace' => 'e'],
            'È É Ê Ë Ē Ĕ Ė Ę Ě' => ['remove' => false, 'replace' => 'E'],
            'ğ ĝ ġ ģ'          => ['remove' => false, 'replace' => 'g'],
            'Ğ Ĝ Ġ Ģ'          => ['remove' => false, 'replace' => 'G'],
            'ħ ĥ'              => ['remove' => false, 'replace' => 'h'],
            'Ĥ Ħ'              => ['remove' => false, 'replace' => 'H'],
            'ì í î ï ĩ ī ĭ į ı' => ['remove' => false, 'replace' => 'i'],
            'Ì Í Î Ï Ĩ Ī Ĭ Į I' => ['remove' => false, 'replace' => 'I'],
            'ĵ'                => ['remove' => false, 'replace' => 'j'],
            'Ĵ'                => ['remove' => false, 'replace' => 'J'],
            'ķ'                => ['remove' => false, 'replace' => 'k'],
            'Ķ'                => ['remove' => false, 'replace' => 'K'],
            'ĺ ļ ľ ŀ ł'        => ['remove' => false, 'replace' => 'l'],
            'Ĺ Ļ Ľ Ŀ Ł'        => ['remove' => false, 'replace' => 'L'],
            'ñ ń ņ ň ŋ'        => ['remove' => false, 'replace' => 'n'],
            'Ñ Ń Ņ Ň Ŋ'        => ['remove' => false, 'replace' => 'N'],
            'ò ó ô õ ö ø ō ŏ ő' => ['remove' => false, 'replace' => 'o'],
            'Ò Ó Ô Õ Ö Ø Ō Ŏ Ő' => ['remove' => false, 'replace' => 'O'],
            'œ'                => ['remove' => false, 'replace' => 'oe'],
            'Œ'                => ['remove' => false, 'replace' => 'OE'],
            'ŕ ŗ ř'            => ['remove' => false, 'replace' => 'r'],
            'Ŕ Ŗ Ř'            => ['remove' => false, 'replace' => 'R'],
            'ś ŝ ş š'          => ['remove' => false, 'replace' => 's'],
            'Ś Ŝ Ş Š'          => ['remove' => false, 'replace' => 'S'],
            'ţ ť ŧ'            => ['remove' => false, 'replace' => 't'],
            'Ţ Ť Ŧ'            => ['remove' => false, 'replace' => 'T'],
            'ù ú û ü ũ ū ŭ ů ű ų' => ['remove' => false, 'replace' => 'u'],
            'Ù Ú Û Ü Ũ Ū Ŭ Ů Ű Ų' => ['remove' => false, 'replace' => 'U'],
            'ŵ'                => ['remove' => false, 'replace' => 'w'],
            'Ŵ'                => ['remove' => false, 'replace' => 'W'],
            'ý ÿ ŷ'            => ['remove' => false, 'replace' => 'y'],
            'Ý Ŷ Ÿ'            => ['remove' => false, 'replace' => 'Y'],
            'ź ż ž'            => ['remove' => false, 'replace' => 'z'],
            'Ź Ż Ž'            => ['remove' => false, 'replace' => 'Z'],
        ];
        $count = 0;
        foreach ($mappings as $group => $data) {
            NameValidation::create([
                'symbols' => $group,'remove' => $data['remove'], 'replace' => $data['replace']
            ]);
        }
        return response()->json(['message' => "$count characters imported."]);
    }
}
