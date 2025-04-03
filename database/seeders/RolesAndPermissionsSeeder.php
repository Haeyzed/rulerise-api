<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
      // Reset cached roles and permissions
      app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

      // Create permissions
      $permissions = [
          // User permissions
          'view users',
          'create users',
          'edit users',
          'delete users',
          'force delete users',
          'restore users',
          'change user password',
          'change user status',
          'verify user email',
          'change user role',
          'view user activity',
          'view user dashboard',
          
          // Job permissions
          'view jobs',
          'create jobs',
          'edit jobs',
          'delete jobs',
          'force delete jobs',
          'restore jobs',
          'feature jobs',
          'mark jobs urgent',
          'change job status',
          'extend job deadline',
          'view job statistics',
          'view trending skills',
          'view job analytics',
          'view recommended candidates',
          
          // Company permissions
          'view companies',
          'create companies',
          'edit companies',
          'delete companies',
          'verify companies',
          'feature companies',
          'view company jobs',
          'view company reviews',
          'view company statistics',
          'view company analytics',
          
          // Candidate permissions
          'view candidates',
          'create candidate profile',
          'edit candidate profile',
          'delete candidate profile',
          'feature candidates',
          'toggle candidate availability',
          'toggle candidate public status',
          'view candidate educations',
          'manage candidate educations',
          'view candidate experiences',
          'manage candidate experiences',
          'view candidate skills',
          'manage candidate skills',
          'view candidate projects',
          'manage candidate projects',
          'view candidate certifications',
          'manage candidate certifications',
          'view candidate languages',
          'manage candidate languages',
          'view candidate resumes',
          'manage candidate resumes',
          'view recommended jobs',
          'view candidate application history',
          'view candidate application stats',
          'view candidate skill suggestions',
          'view candidate profile completion',
          
          // Application permissions
          'view applications',
          'create applications',
          'edit applications',
          'delete applications',
          'manage applications',
          'change application status',
          'view application history',
          'add application notes',
          'withdraw applications',
          'view application statistics',
          
          // Blog permissions
          'view blog posts',
          'create blog posts',
          'edit blog posts',
          'delete blog posts',
          'force delete blog posts',
          'restore blog posts',
          'publish blog posts',
          'unpublish blog posts',
          'archive blog posts',
          'view blog statistics',
          'view trending posts',
          'view featured posts',
          
          // Job Pool permissions
          'view job pools',
          'create job pools',
          'edit job pools',
          'delete job pools',
          'toggle job pool status',
          'view job pool candidates',
          'add candidates to job pool',
          'update candidates in job pool',
          'remove candidates from job pool',
          'change candidate status in job pool',
          'view candidate status history in job pool',
          
          // Role & Permission management
          'view roles',
          'create roles',
          'edit roles',
          'delete roles',
          'view permissions',
          'create permissions',
          'edit permissions',
          'delete permissions',
          'assign roles',
          'remove roles',
          'sync roles',
          'give permissions',
          'revoke permissions',
          'sync permissions',
          
          // Statistics permissions
          'view dashboard statistics',
          'view user statistics',
          'view job statistics',
          'view company statistics',
          'view candidate statistics',
          'view application statistics',
          'view subscription statistics',
          'view revenue statistics',
          'view blog statistics',
          'view job pool statistics',
          
          // Settings permissions
          'view settings',
          'edit settings',
          'view site settings',
          'view seo settings',
          'view email settings',
          'view job settings',
          'view candidate settings',
          'view company settings',
          'view blog settings',
          'clear settings cache',
      ];

      // Remove any duplicates that might exist
      $uniquePermissions = array_unique($permissions);

      foreach ($uniquePermissions as $permission) {
          Permission::create(['name' => $permission]);
      }

      // Create roles and assign permissions
      $adminRole = Role::create(['name' => 'admin']);
      $adminRole->givePermissionTo(Permission::all());

      $employerRole = Role::create(['name' => 'employer']);
      $employerRole->givePermissionTo([
          // Job permissions
          'view jobs',
          'create jobs',
          'edit jobs',
          'delete jobs',
          'feature jobs',
          'mark jobs urgent',
          'change job status',
          'extend job deadline',
          'view job statistics',
          'view trending skills',
          'view job analytics',
          'view recommended candidates',
          
          // Company permissions
          'view companies',
          'create companies',
          'edit companies',
          'view company jobs',
          'view company reviews',
          'view company statistics',
          'view company analytics',
          
          // Candidate permissions
          'view candidates',
          
          // Application permissions
          'view applications',
          'manage applications',
          'change application status',
          'view application history',
          'add application notes',
          'view application statistics',
          
          // Job Pool permissions
          'view job pools',
          'create job pools',
          'edit job pools',
          'delete job pools',
          'toggle job pool status',
          'view job pool candidates',
          'add candidates to job pool',
          'update candidates in job pool',
          'remove candidates from job pool',
          'change candidate status in job pool',
          'view candidate status history in job pool',
          
          // Blog permissions
          'view blog posts',
      ]);

      $candidateRole = Role::create(['name' => 'candidate']);
      $candidateRole->givePermissionTo([
          // Job permissions
          'view jobs',
          
          // Company permissions
          'view companies',
          'view company jobs',
          'view company reviews',
          
          // Candidate permissions
          'create candidate profile',
          'edit candidate profile',
          'toggle candidate availability',
          'toggle candidate public status',
          'view candidate educations',
          'manage candidate educations',
          'view candidate experiences',
          'manage candidate experiences',
          'view candidate skills',
          'manage candidate skills',
          'view candidate projects',
          'manage candidate projects',
          'view candidate certifications',
          'manage candidate certifications',
          'view candidate languages',
          'manage candidate languages',
          'view candidate resumes',
          'manage candidate resumes',
          'view recommended jobs',
          'view candidate application history',
          'view candidate application stats',
          'view candidate skill suggestions',
          'view candidate profile completion',
          
          // Application permissions
          'view applications',
          'create applications',
          'withdraw applications',
          'view application history',
          
          // Blog permissions
          'view blog posts',
      ]);

      // Assign roles to existing users based on their role attribute
      $users = User::all();
      foreach ($users as $user) {
          switch ($user->role) {
              case 'admin':
                  $user->assignRole('admin');
                  break;
              case 'employer':
                  $user->assignRole('employer');
                  break;
              case 'candidate':
                  $user->assignRole('candidate');
                  break;
          }
      }
  }
}