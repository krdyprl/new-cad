<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    private array $availableLocales = ['en', 'id'];

    public function switchLanguage($locale)
    {
        try {
            if (!in_array($locale, $this->availableLocales)) {
                Log::warning('Invalid locale attempted', ['locale' => $locale]);
                return Redirect::back()->with('error', 'Invalid language selection.');
            }
            
            Session::put('locale', $locale);
            App::setLocale($locale);
            
            Log::info('Language switched', ['locale' => $locale]);
            
            return Redirect::back()->with('success', 'Language changed successfully.');
            
        } catch (\Exception $e) {
            Log::error('Language switch failed', [
                'error' => $e->getMessage(),
                'locale' => $locale
            ]);
            
            return Redirect::back()->with('error', 'Failed to change language.');
        }
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }
}
