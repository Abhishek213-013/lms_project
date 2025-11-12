<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'value_bn'];
    protected $table = 'content_management';
    public $timestamps = true;

    /**
     * Simple content getter - if language is bangla return value_bn, else return value
     */
    public static function getContent(string $language = 'bn'): array
    {
        try {
            $contentItems = self::all();
            
            $content = [];
            foreach ($contentItems as $item) {
                if ($language === 'en') {
                    $content[$item->key] = $item->value;
                } else {
                    // For Bengali: Use value_bn if available, otherwise fallback to value
                    $content[$item->key] = !empty($item->value_bn) ? $item->value_bn : $item->value;
                }
            }

            return $content;
        } catch (\Exception $e) {
            Log::error('Error getting content: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get home content - simple approach
     */
    public static function getHomeContent(string $language = 'bn'): array
    {
        try {
            $contentItems = self::where('key', 'like', 'home_%')->get();
            
            $homeContent = [];
            foreach ($contentItems as $item) {
                if ($language === 'en') {
                    $homeContent[$item->key] = $item->value;
                } else {
                    $homeContent[$item->key] = !empty($item->value_bn) ? $item->value_bn : $item->value;
                }
            }

            return $homeContent;
        } catch (\Exception $e) {
            Log::error('Error getting home content: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get about content - simple approach
     */
    public static function getAboutContent(string $language = 'bn'): array
    {
        try {
            $contentItems = self::where('key', 'like', 'about_%')->get();
            
            $aboutContent = [];
            foreach ($contentItems as $item) {
                if ($language === 'en') {
                    $aboutContent[$item->key] = $item->value;
                } else {
                    $aboutContent[$item->key] = !empty($item->value_bn) ? $item->value_bn : $item->value;
                }
            }

            return $aboutContent;
        } catch (\Exception $e) {
            Log::error('Error getting about content: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get single value by key
     */
    public static function getValue(string $key, string $language = 'bn'): string
    {
        try {
            $content = self::where('key', $key)->first();
            
            if (!$content) {
                return '';
            }

            if ($language === 'en') {
                return $content->value;
            } else {
                return !empty($content->value_bn) ? $content->value_bn : $content->value;
            }
        } catch (\Exception $e) {
            Log::error('Error getting content value: ' . $e->getMessage());
            return '';
        }
    }
}