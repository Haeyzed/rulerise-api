<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CandidateService;
use App\Http\Requests\CandidateProfileRequest;
use App\Http\Requests\CandidateEducationRequest;
use App\Http\Requests\CandidateExperienceRequest;
use App\Http\Requests\CandidateSkillRequest;
use App\Http\Requests\CandidateProjectRequest;
use App\Http\Requests\CandidateCertificationRequest;
use App\Http\Requests\CandidateLanguageRequest;
use App\Http\Requests\CandidateResumeRequest;
use App\Http\Resources\CandidateProfileResource;
use App\Http\Resources\CandidateEducationResource;
use App\Http\Resources\CandidateExperienceResource;
use App\Http\Resources\CandidateSkillResource;
use App\Http\Resources\CandidateProjectResource;
use App\Http\Resources\CandidateCertificationResource;
use App\Http\Resources\CandidateLanguageResource;
use App\Http\Resources\CandidateResumeResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobApplicationResource;
use App\Models\CandidateProfile;
use App\Models\CandidateResume;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    /**
     * The candidate service instance.
     *
     * @var CandidateService
     */
    protected CandidateService $candidateService;

    /**
     * Create a new controller instance.
     *
     * @param CandidateService $candidateService
     * @return void
     */
    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
        $this->authorizeResource(CandidateProfile::class, 'candidate');
    }

    /**
     * Display a listing of the candidates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'education_level_id', 'experience_years_min', 'experience_years_max',
            'expected_salary_min', 'expected_salary_max', 'location', 'country',
            'is_remote_preferred', 'is_public', 'is_available', 'is_featured',
            'skill_ids', 'sort_by', 'sort_direction'
        ]);

        $candidates = $this->candidateService->list($filters, $request->input('per_page', 15));

        return response()->paginatedSuccess(
            CandidateProfileResource::collection($candidates),
            'Candidates retrieved successfully'
        );
    }

    /**
     * Store a newly created candidate profile in storage.
     *
     * @param CandidateProfileRequest $request
     * @return JsonResponse
     */
    public function store(CandidateProfileRequest $request): JsonResponse
    {
        $candidate = $this->candidateService->create($request->validated());

        return response()->success(
            new CandidateProfileResource($candidate),
            'Candidate profile created successfully',
            201
        );
    }

    /**
     * Display the specified candidate profile.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function show(CandidateProfile $candidate): JsonResponse
    {
        // Load relationships
        $candidate->load([
            'user', 'educationLevel', 'skills.skill', 'educations', 'experiences',
            'projects', 'certifications', 'languages', 'resumes'
        ]);

        return response()->success(
            new CandidateProfileResource($candidate),
            'Candidate profile retrieved successfully'
        );
    }

    /**
     * Update the specified candidate profile in storage.
     *
     * @param CandidateProfileRequest $request
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function update(CandidateProfileRequest $request, CandidateProfile $candidate): JsonResponse
    {
        $candidate = $this->candidateService->update($candidate, $request->validated());

        return response()->success(
            new CandidateProfileResource($candidate),
            'Candidate profile updated successfully'
        );
    }

    /**
     * Remove the specified candidate profile from storage.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function destroy(CandidateProfile $candidate): JsonResponse
    {
        // This is a soft delete
        $this->candidateService->delete($candidate);

        return response()->success(null, 'Candidate profile deleted successfully');
    }

    /**
     * Get candidate educations.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getEducations(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $educations = $candidate->educations()->orderBy('end_date', 'desc')->get();

        return response()->success(
            CandidateEducationResource::collection($educations),
            'Educations retrieved successfully'
        );
    }

    /**
     * Add or update education for candidate.
     *
     * @param CandidateEducationRequest $request
     * @param CandidateProfile $candidate
     * @param int|null $educationId
     * @return JsonResponse
     */
    public function addOrUpdateEducation(CandidateEducationRequest $request, CandidateProfile $candidate, ?int $educationId = null): JsonResponse
    {
        $this->authorize('update', $candidate);

        $education = $this->candidateService->addOrUpdateEducation($candidate, $request->validated(), $educationId);

        return response()->success(
            new CandidateEducationResource($education),
            $educationId ? 'Education updated successfully' : 'Education added successfully',
            $educationId ? 200 : 201
        );
    }

    /**
     * Remove education from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $educationId
     * @return JsonResponse
     */
    public function removeEducation(CandidateProfile $candidate, int $educationId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeEducation($candidate, $educationId);

        if (!$result) {
            return response()->error('Education not found', 404);
        }

        return response()->success(null, 'Education removed successfully');
    }

    /**
     * Get candidate experiences.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getExperiences(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $experiences = $candidate->experiences()->orderBy('end_date', 'desc')->get();

        return response()->success(
            CandidateExperienceResource::collection($experiences),
            'Experiences retrieved successfully'
        );
    }

    /**
     * Add or update experience for candidate.
     *
     * @param CandidateExperienceRequest $request
     * @param CandidateProfile $candidate
     * @param int|null $experienceId
     * @return JsonResponse
     */
    public function addOrUpdateExperience(CandidateExperienceRequest $request, CandidateProfile $candidate, ?int $experienceId = null): JsonResponse
    {
        $this->authorize('update', $candidate);

        $experience = $this->candidateService->addOrUpdateExperience($candidate, $request->validated(), $experienceId);

        return response()->success(
            new CandidateExperienceResource($experience),
            $experienceId ? 'Experience updated successfully' : 'Experience added successfully',
            $experienceId ? 200 : 201
        );
    }

    /**
     * Remove experience from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $experienceId
     * @return JsonResponse
     */
    public function removeExperience(CandidateProfile $candidate, int $experienceId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeExperience($candidate, $experienceId);

        if (!$result) {
            return response()->error('Experience not found', 404);
        }

        return response()->success(null, 'Experience removed successfully');
    }

    /**
     * Get candidate skills.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getSkills(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $skills = $candidate->skills()->with('skill')->get();

        return response()->success(
            CandidateSkillResource::collection($skills),
            'Skills retrieved successfully'
        );
    }

    /**
     * Add or update skill for candidate.
     *
     * @param CandidateSkillRequest $request
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function addOrUpdateSkill(CandidateSkillRequest $request, CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('update', $candidate);

        $skill = $this->candidateService->addOrUpdateSkill($candidate, $request->validated());

        return response()->success(
            new CandidateSkillResource($skill),
            'Skill added or updated successfully'
        );
    }

    /**
     * Remove skill from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $skillId
     * @return JsonResponse
     */
    public function removeSkill(CandidateProfile $candidate, int $skillId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeSkill($candidate, $skillId);

        if (!$result) {
            return response()->error('Skill not found', 404);
        }

        return response()->success(null, 'Skill removed successfully');
    }

    /**
     * Get candidate projects.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getProjects(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $projects = $candidate->projects()->orderBy('end_date', 'desc')->get();

        return response()->success(
            CandidateProjectResource::collection($projects),
            'Projects retrieved successfully'
        );
    }

    /**
     * Add or update project for candidate.
     *
     * @param CandidateProjectRequest $request
     * @param CandidateProfile $candidate
     * @param int|null $projectId
     * @return JsonResponse
     */
    public function addOrUpdateProject(CandidateProjectRequest $request, CandidateProfile $candidate, ?int $projectId = null): JsonResponse
    {
        $this->authorize('update', $candidate);

        $project = $this->candidateService->addOrUpdateProject($candidate, $request->validated(), $projectId);

        return response()->success(
            new CandidateProjectResource($project),
            $projectId ? 'Project updated successfully' : 'Project added successfully',
            $projectId ? 200 : 201
        );
    }

    /**
     * Remove project from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $projectId
     * @return JsonResponse
     */
    public function removeProject(CandidateProfile $candidate, int $projectId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeProject($candidate, $projectId);

        if (!$result) {
            return response()->error('Project not found', 404);
        }

        return response()->success(null, 'Project removed successfully');
    }

    /**
     * Get candidate certifications.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getCertifications(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $certifications = $candidate->certifications()->orderBy('issue_date', 'desc')->get();

        return response()->success(
            CandidateCertificationResource::collection($certifications),
            'Certifications retrieved successfully'
        );
    }

    /**
     * Add or update certification for candidate.
     *
     * @param CandidateCertificationRequest $request
     * @param CandidateProfile $candidate
     * @param int|null $certificationId
     * @return JsonResponse
     */
    public function addOrUpdateCertification(CandidateCertificationRequest $request, CandidateProfile $candidate, ?int $certificationId = null): JsonResponse
    {
        $this->authorize('update', $candidate);

        $certification = $this->candidateService->addOrUpdateCertification($candidate, $request->validated(), $certificationId);

        return response()->success(
            new CandidateCertificationResource($certification),
            $certificationId ? 'Certification updated successfully' : 'Certification added successfully',
            $certificationId ? 200 : 201
        );
    }

    /**
     * Remove certification from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $certificationId
     * @return JsonResponse
     */
    public function removeCertification(CandidateProfile $candidate, int $certificationId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeCertification($candidate, $certificationId);

        if (!$result) {
            return response()->error('Certification not found', 404);
        }

        return response()->success(null, 'Certification removed successfully');
    }

    /**
     * Get candidate languages.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getLanguages(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $languages = $candidate->languages()->with('language')->get();

        return response()->success(
            CandidateLanguageResource::collection($languages),
            'Languages retrieved successfully'
        );
    }

    /**
     * Add or update language for candidate.
     *
     * @param CandidateLanguageRequest $request
     * @param CandidateProfile $candidate
     * @param int|null $languageId
     * @return JsonResponse
     */
    public function addOrUpdateLanguage(CandidateLanguageRequest $request, CandidateProfile $candidate, ?int $languageId = null): JsonResponse
    {
        $this->authorize('update', $candidate);

        $language = $this->candidateService->addOrUpdateLanguage($candidate, $request->validated(), $languageId);

        return response()->success(
            new CandidateLanguageResource($language),
            $languageId ? 'Language updated successfully' : 'Language added successfully',
            $languageId ? 200 : 201
        );
    }

    /**
     * Remove language from candidate.
     *
     * @param CandidateProfile $candidate
     * @param int $languageId
     * @return JsonResponse
     */
    public function removeLanguage(CandidateProfile $candidate, int $languageId): JsonResponse
    {
        $this->authorize('update', $candidate);

        $result = $this->candidateService->removeLanguage($candidate, $languageId);

        if (!$result) {
            return response()->error('Language not found', 404);
        }

        return response()->success(null, 'Language removed successfully');
    }

    /**
     * Get candidate resumes.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getResumes(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $resumes = $candidate->resumes()->orderBy('created_at', 'desc')->get();

        return response()->success(
            CandidateResumeResource::collection($resumes),
            'Resumes retrieved successfully'
        );
    }

    /**
     * Upload resume for candidate.
     *
     * @param CandidateResumeRequest $request
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function uploadResume(CandidateResumeRequest $request, CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('update', $candidate);

        $resume = $this->candidateService->uploadResume($candidate, $request->file('file'), $request->validated());

        return response()->success(
            new CandidateResumeResource($resume),
            'Resume uploaded successfully',
            201
        );
    }

    /**
     * Delete resume.
     *
     * @param CandidateProfile $candidate
     * @param CandidateResume $resume
     * @return JsonResponse
     */
    public function deleteResume(CandidateProfile $candidate, CandidateResume $resume): JsonResponse
    {
        $this->authorize('update', $candidate);

        // Check if resume belongs to candidate
        if ($resume->candidate_profile_id !== $candidate->id) {
            return response()->error('Resume not found', 404);
        }

        $result = $this->candidateService->deleteResume($resume);

        return response()->success(null, 'Resume deleted successfully');
    }

    /**
     * Set resume as primary.
     *
     * @param CandidateProfile $candidate
     * @param CandidateResume $resume
     * @return JsonResponse
     */
    public function setResumePrimary(CandidateProfile $candidate, CandidateResume $resume): JsonResponse
    {
        $this->authorize('update', $candidate);

        // Check if resume belongs to candidate
        if ($resume->candidate_profile_id !== $candidate->id) {
            return response()->error('Resume not found', 404);
        }

        $this->candidateService->setResumePrimary($resume);

        return response()->success(null, 'Resume set as primary successfully');
    }

    /**
     * Toggle candidate availability.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function toggleAvailability(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('update', $candidate);

        $this->candidateService->toggleAvailability($candidate);

        return response()->success(
            ['is_available' => $candidate->is_available],
            'Candidate availability toggled successfully'
        );
    }

    /**
     * Toggle candidate public status.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function togglePublic(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('update', $candidate);

        $this->candidateService->togglePublic($candidate);

        return response()->success(
            ['is_public' => $candidate->is_public],
            'Candidate public status toggled successfully'
        );
    }

    /**
     * Get recommended jobs for candidate.
     *
     * @param CandidateProfile $candidate
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendedJobs(CandidateProfile $candidate, Request $request): JsonResponse
    {
        $this->authorize('view', $candidate);

        $limit = $request->input('limit', 10);
        $jobs = $this->candidateService->getRecommendedJobs($candidate, $limit);

        return response()->success(
            JobResource::collection($jobs),
            'Recommended jobs retrieved successfully'
        );
    }

    /**
     * Get candidate job application history.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getJobApplicationHistory(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $applications = $this->candidateService->getJobApplicationHistory($candidate);

        return response()->success(
            JobApplicationResource::collection($applications),
            'Job application history retrieved successfully'
        );
    }

    /**
     * Get candidate job application statistics.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function getJobApplicationStats(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $stats = $this->candidateService->getJobApplicationStats($candidate);

        return response()->success($stats, 'Job application statistics retrieved successfully');
    }

    /**
     * Get skill suggestions for candidate.
     *
     * @param CandidateProfile $candidate
     * @param Request $request
     * @return JsonResponse
     */
    public function getSkillSuggestions(CandidateProfile $candidate, Request $request): JsonResponse
    {
        $this->authorize('view', $candidate);

        $limit = $request->input('limit', 10);
        $skills = $this->candidateService->getSkillSuggestions($candidate, $limit);

        return response()->success($skills, 'Skill suggestions retrieved successfully');
    }

    /**
     * Calculate profile completion percentage.
     *
     * @param CandidateProfile $candidate
     * @return JsonResponse
     */
    public function calculateProfileCompletion(CandidateProfile $candidate): JsonResponse
    {
        $this->authorize('view', $candidate);

        $completion = $this->candidateService->calculateProfileCompletion($candidate);

        return response()->success(
            ['percentage' => $completion],
            'Profile completion calculated successfully'
        );
    }
}

