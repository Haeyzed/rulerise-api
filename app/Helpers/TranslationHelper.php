<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class TranslationHelper
{
    /**
     * Translate a message to the current locale.
     *
     * @param string $message
     * @return string
     */
    public static function translateMessage(string $message): string
    {
        $locale = App::getLocale();
        
        // If locale is English, no need to translate
        if ($locale === 'en') {
            return $message;
        }
        
        // Cache key for this translation
        $cacheKey = "translation:{$locale}:{$message}";
        
        // Check if translation is cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $translator = new GoogleTranslate($locale);
            $translator->setSource('en');
            
            $translatedMessage = $translator->translate($message);
            
            // Cache the translation for 24 hours
            Cache::put($cacheKey, $translatedMessage, now()->addHours(24));
            
            return $translatedMessage;
        } catch (\Exception $e) {
            // If translation fails, return original message
            return $message;
        }
    }
    
    /**
     * Translate validation errors to the current locale.
     *
     * @param array $errors
     * @return array
     */
    public static function translateErrors(array $errors): array
    {
        $locale = App::getLocale();
        
        // If locale is English, no need to translate
        if ($locale === 'en') {
            return $errors;
        }
        
        try {
            $translator = new GoogleTranslate($locale);
            $translator->setSource('en');
            
            $translatedErrors = [];
            
            foreach ($errors as $field => $messages) {
                $translatedMessages = [];
                
                foreach ($messages as $message) {
                    // Cache key for this error message
                    $cacheKey = "translation:{$locale}:{$message}";
                    
                    // Check if translation is cached
                    if (Cache::has($cacheKey)) {
                        $translatedMessages[] = Cache::get($cacheKey);
                    } else {
                        $translatedMessage = $translator->translate($message);
                        
                        // Cache the translation for 24 hours
                        Cache::put($cacheKey, $translatedMessage, now()->addHours(24));
                        
                        $translatedMessages[] = $translatedMessage;
                    }
                }
                
                $translatedErrors[$field] = $translatedMessages;
            }
            
            return $translatedErrors;
        } catch (\Exception $e) {
            // If translation fails, return original errors
            return $errors;
        }
    }
}

