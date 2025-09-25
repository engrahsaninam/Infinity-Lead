<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Infinity extends Model
{
    //
    const _STORAGE_PROFILE = '/storage/profile/';
    const _STORAGE_LINKEDIN_EXPORT = '/storage/linkedin_exports/';
    const _STORAGE_CSV = '/storage/csv/';

    const _PUBLIC_TESTIMONIAL_PROFILE = '/public/testimonial_profile/';
    const _PUBLIC_TEAM_PROFILE = '/public/team_profile/';
    const _PUBLIC_PROFILE = '/public/profile/';
    const _PUBLIC_LINKEDIN_EXPORT = '/public/linkedin_exports/';
    const _PUBLIC_CSV = '/public/csv/';
    const _PUBLIC_CAMP_ATTACH = '/public/attachments/';

    static function upload($file, $publicPath, $fileName = '')
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        // $originalFileName = time() . '-' . $originalFileName;
        $originalFileName = $originalFileName . "_" . uniqid();
        if (!$fileName) {
            $newFileName = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $originalFileName)) . '.' . $extension;
        } else {
            $newFileName = $fileName;
        }
        $fullPath = str_replace('public', '', $publicPath . $newFileName);
        $url = str_replace('public', 'storage', $publicPath) . $newFileName;
        Storage::disk('public')->put($fullPath, File::get($file));
        return $url;
    }
    public static function stamp($stamp)
    {
        $timestamp = strtotime($stamp);
        $carbonDate = Carbon::createFromTimestamp($timestamp);

        return [
            'Y' => date('Y', $timestamp), // Year
            'y' => date('y', $timestamp), // Short Year
            'm' => date('m', $timestamp), // Month (01-12)
            'M' => date('M', $timestamp), // Short Month Name
            'F' => date('F', $timestamp), // Full Month Name
            'd' => date('d', $timestamp), // Day (01-31)
            'D' => date('D', $timestamp), // Short Day Name (Mon)
            'l' => date('l', $timestamp), // Full Day Name (Monday)
            'H' => date('H', $timestamp), // 24-hour format (00-23)
            'h' => date('h', $timestamp), // 12-hour format (01-12)
            'i' => date('i', $timestamp), // Minutes (00-59)
            's' => date('s', $timestamp), // Seconds (00-59)
            'A' => date('A', $timestamp), // AM/PM
            'a' => date('a', $timestamp), // am/pm
            'timestamp' => $stamp,
            'human_diff' => $carbonDate->diffForHumans(), // Carbon Human Diff

        ];
    }

}
