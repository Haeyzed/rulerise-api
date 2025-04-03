<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * @var SettingService
     */
    protected SettingService $settingService;

    /**
     * SettingController constructor.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Get all settings.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->all();
        
        return response()->success($settings, 'Settings retrieved successfully');
    }

    /**
     * Get a specific setting.
     *
     * @param string $key
     * @return JsonResponse
     */
    public function show(string $key): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $setting = $this->settingService->get($key);
        
        if ($setting === null) {
            return response()->error('Setting not found', 404);
        }
        
        return response()->success(['key' => $key, 'value' => $setting], 'Setting retrieved successfully');
    }

    /**
     * Update a specific setting.
     *
     * @param Request $request
     * @param string $key
     * @return JsonResponse
     */
    public function update(Request $request, string $key): JsonResponse
    {
        if (Gate::denies('manage-settings')) {
            return response()->forbidden('You do not have permission to update settings');
        }
        
        $request->validate([
            'value' => 'required',
        ]);
        
        $this->settingService->set($key, $request->input('value'));
        
        return response()->success(
            ['key' => $key, 'value' => $request->input('value')],
            'Setting updated successfully'
        );
    }

    /**
     * Update multiple settings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMultiple(Request $request): JsonResponse
    {
        if (Gate::denies('manage-settings')) {
            return response()->forbidden('You do not have permission to update settings');
        }
        
        $request->validate([
            'settings' => 'required|array',
        ]);
        
        $this->settingService->setMultiple($request->input('settings'));
        
        return response()->success($request->input('settings'), 'Settings updated successfully');
    }

    /**
     * Get site settings.
     *
     * @return JsonResponse
     */
    public function getSiteSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getSiteSettings();
        
        return response()->success($settings, 'Site settings retrieved successfully');
    }

    /**
     * Get SEO settings.
     *
     * @return JsonResponse
     */
    public function getSeoSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getSeoSettings();
        
        return response()->success($settings, 'SEO settings retrieved successfully');
    }

    /**
     * Get email settings.
     *
     * @return JsonResponse
     */
    public function getEmailSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getEmailSettings();
        
        return response()->success($settings, 'Email settings retrieved successfully');
    }

    /**
     * Get job settings.
     *
     * @return JsonResponse
     */
    public function getJobSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getJobSettings();
        
        return response()->success($settings, 'Job settings retrieved successfully');
    }

    /**
     * Get candidate settings.
     *
     * @return JsonResponse
     */
    public function getCandidateSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getCandidateSettings();
        
        return response()->success($settings, 'Candidate settings retrieved successfully');
    }

    /**
     * Get company settings.
     *
     * @return JsonResponse
     */
    public function getCompanySettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getCompanySettings();
        
        return response()->success($settings, 'Company settings retrieved successfully');
    }

    /**
     * Get blog settings.
     *
     * @return JsonResponse
     */
    public function getBlogSettings(): JsonResponse
    {
        if (Gate::denies('view-settings')) {
            return response()->forbidden('You do not have permission to view settings');
        }
        
        $settings = $this->settingService->getBlogSettings();
        
        return response()->success($settings, 'Blog settings retrieved successfully');
    }

    /**
     * Clear settings cache.
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        if (Gate::denies('manage-settings')) {
            return response()->forbidden('You do not have permission to clear settings cache');
        }
        
        $this->settingService->clearCache();
        
        return response()->success(null, 'Settings cache cleared successfully');
    }
}

