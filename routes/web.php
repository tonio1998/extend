<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DocumentTrackingController;
use App\Http\Controllers\DSS\CentralizedDocumentUploadController;
use App\Http\Controllers\DSS\CentralizedSignatoryController;
use App\Http\Controllers\DSS\DesignationManagementController;
use App\Http\Controllers\DSS\DocumentSignatureInboxController;
use App\Http\Controllers\DSS\DocumentViewerController;
use App\Http\Controllers\DSS\DSSDashboardController;
use App\Http\Controllers\DSS\EmployeeManagementController;
use App\Http\Controllers\DSS\ExtensionPriorityController;
use App\Http\Controllers\DSS\InstitutionSettingsController;
use App\Http\Controllers\DSS\RIE\ActivityDesignController;
use App\Http\Controllers\DSS\RIE\ExtensionBudgetController;
use App\Http\Controllers\DSS\RIE\ExtensionCalendarController;
use App\Http\Controllers\DSS\RIE\ExtensionPlanController;
use App\Http\Controllers\DSS\RIE\ExtensionProposalController;
use App\Http\Controllers\DSS\RIE\ExtensionTrackingController;
use App\Http\Controllers\DSS\RIE\UnitMonitoringController;
use App\Http\Controllers\DSS\RolePermissionController;
use App\Http\Controllers\DSS\Select2Controller;
use App\Http\Controllers\DSS\ThemeCustomizerController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/track/{tracking_number?}', [DocumentTrackingController::class, 'showTracker'])->name('document.track');
Route::get('/api/track/{tracking_number}', [DocumentTrackingController::class, 'trackApi'])->name('document.track.api');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->prefix('dss')->name('dss.')->group(function () {
    Route::get('/dashboard', [DSSDashboardController::class, 'index'])->name('dashboard');
    Route::get('/signatures', [DocumentSignatureInboxController::class, 'index'])->name('signatures.index');

    Route::prefix('select2')->name('select2.')->group(function () {
        Route::get('/units', [Select2Controller::class, 'units'])->name('units');
        Route::get('/employees', [Select2Controller::class, 'employees'])->name('employees');
        Route::get('/users', [Select2Controller::class, 'users'])->name('users');
        Route::get('/doc-types', [Select2Controller::class, 'docTypes'])->name('doc_types');
        Route::get('/actions', [Select2Controller::class, 'actions'])->name('actions');
        Route::get('/departments', [Select2Controller::class, 'departments'])->name('departments');
        Route::get('/academic-programs', [Select2Controller::class, 'academicPrograms'])->name('academic_programs');
    });
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/repository', [CentralizedDocumentUploadController::class, 'index'])->name('repository');
        Route::get('/suggestions', [CentralizedDocumentUploadController::class, 'suggestions'])->name('suggestions');

        Route::prefix('uploads')->name('upload.')->group(function () {
            Route::get('/', [CentralizedDocumentUploadController::class, 'index'])->name('index');
            Route::post('/', [CentralizedDocumentUploadController::class, 'upload'])->name('store');
            Route::get('/list', [CentralizedDocumentUploadController::class, 'list'])->name('list');
            Route::get('/{id}/download', [CentralizedDocumentUploadController::class, 'download'])->name('download');
            Route::get('/{id}/preview', [CentralizedDocumentUploadController::class, 'preview'])->name('preview');
            Route::delete('/{id}', [CentralizedDocumentUploadController::class, 'destroy'])->name('destroy');
        });

        Route::get('/{type}/{id}/view', [DocumentViewerController::class, 'view'])->name('view');
        Route::get('/{type}/{id}/pdf', [DocumentViewerController::class, 'pdf'])->name('pdf');
        Route::get('/{type}/{id}/preview', [DocumentViewerController::class, 'preview'])->name('preview');
        Route::get('/{type}/{id}/audit-trail', [DocumentViewerController::class, 'getAuditTrail'])->name('audit_trail');
        Route::get('/{type}/{id}/signatories', [DocumentViewerController::class, 'getSignatories'])->name('signatories');
        Route::get('/{type}/{id}/signatories/edit', [CentralizedSignatoryController::class, 'editDocumentSignatories'])->name('signatories.edit');
        Route::post('/{type}/{id}/signatories', [CentralizedSignatoryController::class, 'updateDocumentSignatoriesWorkflow'])->name('signatories.update');
        Route::get('/{type}/{id}/signatories/suggest', [CentralizedSignatoryController::class, 'suggestDocumentSignatories'])->name('signatories.suggest');
        Route::get('/{type}/{id}/barcode', [DocumentViewerController::class, 'barcodeStamp'])->name('barcode');
        Route::get('/{type}/{id}/comments', [DocumentViewerController::class, 'getComments'])->name('comments.index');
        Route::get('/{type}/{id}/comments/list', [DocumentViewerController::class, 'getComments'])->name('comments.get');
        Route::post('/{type}/{id}/comments', [DocumentViewerController::class, 'storeComment'])->name('comments.store');
        Route::post('/{type}/{id}/sign', [DocumentViewerController::class, 'signDocument'])->name('sign');
        Route::post('/{type}/{id}/request-revision', [DocumentViewerController::class, 'requestRevision'])->name('request_revision');
    });
    Route::get('/documents-repository', [CentralizedDocumentUploadController::class, 'index'])->name('document.repository');

    Route::prefix('rie')->name('rie.')->group(function () {
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/data', [ExtensionPlanController::class, 'data'])->name('data');
            Route::get('/', [ExtensionPlanController::class, 'index'])->name('index');
            Route::get('/my-proposals', [ExtensionPlanController::class, 'myPlans'])->name('my');
            Route::get('/create', [ExtensionPlanController::class, 'create'])->name('create');
            Route::post('/', [ExtensionPlanController::class, 'store'])->name('store');
            Route::get('/{id}', [ExtensionPlanController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ExtensionPlanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ExtensionPlanController::class, 'update'])->name('update');
            Route::delete('/{id}', [ExtensionPlanController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/pdf', [ExtensionPlanController::class, 'pdf'])->name('pdf');
            Route::get('/{id}/signatories', [ExtensionPlanController::class, 'configureSignatories'])->name('signatories.edit');
            Route::post('/{id}/submit-for-review', [ExtensionPlanController::class, 'submitForReview'])->name('submit_for_review');
            Route::get('/fetch-signatories', [ExtensionPlanController::class, 'getSignatoriesByProgram'])->name('signatories_by_program');
            Route::post('/{id}/connect-tracking', [ExtensionPlanController::class, 'connectTracking'])->name('connect_tracking');
            Route::get('/{id}/fetch-tracking', [ExtensionPlanController::class, 'fetchLiveTracking'])->name('fetch_tracking');
            Route::post('/{id}/activities/{activityId}/upload-document', [ExtensionPlanController::class, 'uploadActivityDocument'])->name('activity.upload_document');
            Route::delete('/{id}/activities/{activityId}/documents/{docId?}', [ExtensionPlanController::class, 'deleteActivityDocument'])->name('activity.delete_document');
            Route::delete('/{id}/activities/{activityId}/delete-document', [ExtensionPlanController::class, 'deleteActivityDocument'])->name('activity.delete_document_legacy');
        });

        Route::prefix('proposals')->name('proposals.')->group(function () {
            Route::get('/data', [ExtensionProposalController::class, 'data'])->name('data');
            Route::get('/', [ExtensionProposalController::class, 'index'])->name('index');
            Route::get('/my-proposals', [ExtensionProposalController::class, 'myProposals'])->name('my');
            Route::get('/create', [ExtensionProposalController::class, 'create'])->name('create');
            Route::post('/', [ExtensionProposalController::class, 'store'])->name('store');
            Route::get('/{id}', [ExtensionProposalController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ExtensionProposalController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ExtensionProposalController::class, 'update'])->name('update');
            Route::get('/{id}/pdf', [ExtensionProposalController::class, 'pdf'])->name('pdf');
            Route::get('/{id}/activity-design', [ExtensionProposalController::class, 'activityDesign'])->name('activity_design');
            Route::post('/{id}/sign', [ExtensionProposalController::class, 'sign'])->name('sign');
            Route::post('/{id}/technical', [ExtensionProposalController::class, 'updateTechnical'])->name('technical.update');
            Route::get('/{id}/signatories', [ExtensionProposalController::class, 'configureSignatories'])->name('signatories.edit');
            Route::post('/{id}/signatories', [ExtensionProposalController::class, 'updateSignatories'])->name('signatories.update');
            Route::get('/{id}/suggest-signatories', [ExtensionProposalController::class, 'getSuggestedSignatories'])->name('signatories.suggest');
            Route::post('/{id}/submit-for-review', [ExtensionProposalController::class, 'submitForReview'])->name('submit_for_review');
            Route::post('/{id}/generate-sdg-narrative', [ExtensionProposalController::class, 'generateSdgNarrative'])->name('generate_sdg_narrative');
            Route::post('/{id}/connect-tracking', [ExtensionProposalController::class, 'connectTracking'])->name('connect_tracking');
            Route::get('/{id}/fetch-tracking', [ExtensionProposalController::class, 'fetchLiveTracking'])->name('fetch_tracking');
            Route::post('/{id}/accomplishment', [ExtensionProposalController::class, 'updateAccomplishment'])->name('accomplishment.update');
            Route::get('/{id}/accomplishment/report', [ExtensionProposalController::class, 'accomplishmentReport'])->name('accomplishment.report');
            Route::get('/{id}/accomplishment/pdf', [ExtensionProposalController::class, 'accomplishmentPdf'])->name('accomplishment.pdf');
            Route::post('/{id}/accomplishment/generate-narrative', [ExtensionProposalController::class, 'generateAccomplishmentNarrative'])->name('accomplishment.generate_narrative');
        });

        Route::prefix('activity-designs')->name('activity_designs.')->group(function () {
            Route::get('/', [ActivityDesignController::class, 'index'])->name('index');
            Route::get('/create', [ActivityDesignController::class, 'create'])->name('create');
            Route::post('/', [ActivityDesignController::class, 'store'])->name('store');
            Route::get('/{id}', [ActivityDesignController::class, 'show'])->name('show');
            Route::get('/{id}/signatories', [ActivityDesignController::class, 'configureSignatories'])->name('signatories.edit');
            Route::post('/{id}/sync', [ActivityDesignController::class, 'sync'])->name('sync');
            Route::get('/{id}/pdf', [ActivityDesignController::class, 'pdf'])->name('pdf');
            Route::post('/{id}/signatories', [ActivityDesignController::class, 'updateSignatories'])->name('signatories.update');
            Route::get('/{id}/suggest-signatories', [ActivityDesignController::class, 'getSuggestedSignatories'])->name('signatories.suggest');
            Route::post('/{id}/sign', [ActivityDesignController::class, 'sign'])->name('sign');
        });

        Route::prefix('budgets')->name('budgets.')->group(function () {
            Route::get('/', [ExtensionBudgetController::class, 'index'])->name('index');
            Route::get('/create', [ExtensionBudgetController::class, 'create'])->name('create');
            Route::post('/', [ExtensionBudgetController::class, 'store'])->name('store');
            Route::get('/export', [ExtensionBudgetController::class, 'export'])->name('export');
            Route::get('/{id}', [ExtensionBudgetController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ExtensionBudgetController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ExtensionBudgetController::class, 'update'])->name('update');
            Route::delete('/{id}', [ExtensionBudgetController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/realign', [ExtensionBudgetController::class, 'realign'])->name('realign');
        });

        Route::prefix('tracking')->name('tracking.')->group(function () {
            Route::get('/', [ExtensionTrackingController::class, 'index'])->name('index');
            Route::get('/data', [ExtensionTrackingController::class, 'data'])->name('data');
            Route::post('/connect', [ExtensionTrackingController::class, 'connect'])->name('connect');
            Route::post('/disconnect', [ExtensionTrackingController::class, 'disconnect'])->name('disconnect');
        });

        Route::prefix('calendar')->name('calendar.')->group(function () {
            Route::get('/', [ExtensionCalendarController::class, 'index'])->name('index');
            Route::get('/events', [ExtensionCalendarController::class, 'events'])->name('events');
        });

        Route::prefix('unit-monitoring')->name('unit_monitoring.')->group(function () {
            Route::get('/', [UnitMonitoringController::class, 'index'])->name('index');
            Route::get('/export', [UnitMonitoringController::class, 'export'])->name('export');
            Route::get('/units/{id}', [UnitMonitoringController::class, 'unitTransactions'])->name('unit_transactions');
            Route::get('/leaders/{name}', [UnitMonitoringController::class, 'leaderTransactions'])->name('leader_transactions');
            Route::post('/proposals/{id}/impact-assessment', [UnitMonitoringController::class, 'recordImpactAssessment'])->name('record_impact');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/institution', [InstitutionSettingsController::class, 'index'])->name('institution');
        Route::get('/document-codes', [InstitutionSettingsController::class, 'documentCodes'])->name('document_codes');
        Route::post('/document-codes', [InstitutionSettingsController::class, 'updateDocumentCodes'])->name('document_codes.update');
        Route::get('/institution/campuses', [InstitutionSettingsController::class, 'campuses'])->name('institution.campuses');
        Route::get('/institution/doc-params', [InstitutionSettingsController::class, 'docParams'])->name('institution.doc_params');
        Route::post('/institution/profile', [InstitutionSettingsController::class, 'updateProfile'])->name('institution.profile');
        Route::post('/institution/campus', [InstitutionSettingsController::class, 'storeCampus'])->name('institution.campus');
        Route::put('/institution/campus/{id}', [InstitutionSettingsController::class, 'updateCampus'])->name('institution.campus.update');
        Route::delete('/institution/campus/{id}', [InstitutionSettingsController::class, 'destroyCampus'])->name('institution.campus.destroy');
        Route::post('/institution/department', [InstitutionSettingsController::class, 'storeDepartment'])->name('institution.department');
        Route::post('/institution/signatory', [InstitutionSettingsController::class, 'storeSignatory'])->name('institution.signatory');
        Route::post('/institution/document-settings', [InstitutionSettingsController::class, 'updateDocumentSettings'])->name('institution.document_settings');
        Route::post('/institution/signatory-template', [InstitutionSettingsController::class, 'storeSignatoryTemplate'])->name('institution.signatory_template');
        Route::patch('/institution/signatory-template/{id}', [InstitutionSettingsController::class, 'updateSignatoryTemplate'])->name('institution.signatory_template.update');
        Route::post('/institution/signatory-template/reorder', [InstitutionSettingsController::class, 'reorderSignatories'])->name('institution.signatory_template.reorder');
        Route::delete('/institution/signatory-template/{id}', [InstitutionSettingsController::class, 'destroySignatoryTemplate'])->name('institution.signatory_template.destroy');
        Route::get('/institution/template-studio', [InstitutionSettingsController::class, 'templateStudio'])->name('institution.template_studio');
        Route::post('/institution/upload-template-logo', [InstitutionSettingsController::class, 'uploadTemplateLogo'])->name('institution.upload_template_logo');
        Route::get('/institution/preview-pdf', [InstitutionSettingsController::class, 'previewPdf'])->name('institution.preview_pdf');

        Route::get('/signatories', [CentralizedSignatoryController::class, 'index'])->name('signatories.index');
        Route::post('/signatories', [CentralizedSignatoryController::class, 'store'])->name('signatories.store');
        Route::post('/signatories/document-type', [CentralizedSignatoryController::class, 'storeDocumentType'])->name('signatories.document_type.store');
        Route::post('/signatories/update-layout', [CentralizedSignatoryController::class, 'updateLayout'])->name('signatories.update_layout');
        Route::post('/signatories/quick-update/{id}', [CentralizedSignatoryController::class, 'updateItemQuick'])->name('signatories.quick_update');
        Route::put('/signatories/{id}', [CentralizedSignatoryController::class, 'update'])->name('signatories.update');
        Route::delete('/signatories/{id}', [CentralizedSignatoryController::class, 'destroy'])->name('signatories.destroy');
        Route::post('/signatories/seed-defaults', [CentralizedSignatoryController::class, 'seedDefaults'])->name('signatories.seed_defaults');
        Route::post('/signatories/update-document', [CentralizedSignatoryController::class, 'updateDocumentSignatories'])->name('signatories.update_document');

        Route::get('/theme', [ThemeCustomizerController::class, 'index'])->name('theme');
        Route::post('/theme/update', [ThemeCustomizerController::class, 'update'])->name('theme.update');
        Route::post('/theme/ui-template', [ThemeCustomizerController::class, 'switchUiTemplate'])->name('theme.ui_template');
        Route::post('/theme/upload-logo', [ThemeCustomizerController::class, 'uploadLogo'])->name('theme.upload_logo');
        Route::delete('/theme/remove-logo', [ThemeCustomizerController::class, 'removeLogo'])->name('theme.remove_logo');

        Route::get('/users', [RolePermissionController::class, 'usersIndex'])->name('users.index');
        Route::get('/users/{id}/permissions', [RolePermissionController::class, 'getUserPermissions'])->name('users.permissions');
        Route::post('/users/assign', [RolePermissionController::class, 'assignUserRole'])->name('users.assign');
        Route::get('/users/{id}/assign', [RolePermissionController::class, 'assignUserAccessPage'])->name('users.assign_page');
        Route::post('/users/{id}/toggle-role', [RolePermissionController::class, 'toggleUserRoleAjax'])->name('users.toggle_role');
        Route::post('/users/{id}/toggle-permission', [RolePermissionController::class, 'toggleUserPermissionAjax'])->name('users.toggle_permission');
        Route::post('/users/{id}/reset-password', [RolePermissionController::class, 'resetUserPassword'])->name('users.reset_password');
        Route::post('/users/{id}/toggle-status', [RolePermissionController::class, 'toggleUserStatus'])->name('users.toggle_status');
        Route::put('/users/{id}', [RolePermissionController::class, 'updateUser'])->name('users.update');

        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{id}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{id}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
        Route::get('/roles/{id}/configure', [RolePermissionController::class, 'configureRole'])->name('roles.configure');
        Route::post('/roles/{id}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions');
        Route::post('/roles/{id}/personnel', [RolePermissionController::class, 'updateRolePersonnel'])->name('roles.personnel');
        Route::post('/roles/assign-user', [RolePermissionController::class, 'assignUserRole'])->name('roles.assign_user');

        Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('permissions.index');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::put('/permissions/{id}', [RolePermissionController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{id}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');
        Route::post('/roles/permissions/store', [RolePermissionController::class, 'storePermission'])->name('roles.permissions.store');

        Route::get('/employees/data', [EmployeeManagementController::class, 'data'])->name('employees.data');
        Route::get('/employees/export', [EmployeeManagementController::class, 'exportCsv'])->name('employees.export');
        Route::get('/employees', [EmployeeManagementController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeManagementController::class, 'create'])->name('employees.create');
        Route::get('/employees/{id}', [EmployeeManagementController::class, 'show'])->name('employees.show');
        Route::get('/employees/{id}/edit', [EmployeeManagementController::class, 'edit'])->name('employees.edit');
        Route::get('/employees/{id}/json', [EmployeeManagementController::class, 'showJson'])->name('employees.json');
        Route::post('/employees', [EmployeeManagementController::class, 'store'])->name('employees.store');
        Route::put('/employees/{id}', [EmployeeManagementController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}', [EmployeeManagementController::class, 'destroy'])->name('employees.destroy');
        Route::post('/employees/{id}/generate-user', [EmployeeManagementController::class, 'generateUserAccess'])->name('employees.generate_user');
        Route::post('/employees/{id}/revoke-user', [EmployeeManagementController::class, 'revokeUserAccess'])->name('employees.revoke_user');
        Route::post('/employees/{id}/toggle-status', [EmployeeManagementController::class, 'toggleStatus'])->name('employees.toggle_status');

        Route::get('/designations', [DesignationManagementController::class, 'index'])->name('designations.index');
        Route::get('/designations/json', [DesignationManagementController::class, 'listJson'])->name('designations.json');
        Route::post('/designations', [DesignationManagementController::class, 'store'])->name('designations.store');
        Route::put('/designations/{id}', [DesignationManagementController::class, 'update'])->name('designations.update');
        Route::delete('/designations/{id}', [DesignationManagementController::class, 'destroy'])->name('designations.destroy');
        Route::post('/designations/{id}/toggle-status', [DesignationManagementController::class, 'toggleStatus'])->name('designations.toggle_status');

        Route::get('/rie-priorities', [ExtensionPriorityController::class, 'index'])->name('rie_priorities.index');
        Route::post('/rie-priorities/banner-programs', [ExtensionPriorityController::class, 'storeBannerProgram'])->name('rie_priorities.banner_programs.store');
        Route::put('/rie-priorities/banner-programs/{id}', [ExtensionPriorityController::class, 'updateBannerProgram'])->name('rie_priorities.banner_programs.update');
        Route::post('/rie-priorities/banner-programs/{id}/toggle-status', [ExtensionPriorityController::class, 'toggleBannerProgramStatus'])->name('rie_priorities.banner_programs.toggle_status');
        Route::delete('/rie-priorities/banner-programs/{id}', [ExtensionPriorityController::class, 'destroyBannerProgram'])->name('rie_priorities.banner_programs.destroy');

        Route::post('/rie-priorities/agendas', [ExtensionPriorityController::class, 'storeAgenda'])->name('rie_priorities.agendas.store');
        Route::put('/rie-priorities/agendas/{id}', [ExtensionPriorityController::class, 'updateAgenda'])->name('rie_priorities.agendas.update');
        Route::post('/rie-priorities/agendas/{id}/toggle-status', [ExtensionPriorityController::class, 'toggleAgendaStatus'])->name('rie_priorities.agendas.toggle_status');
        Route::delete('/rie-priorities/agendas/{id}', [ExtensionPriorityController::class, 'destroyAgenda'])->name('rie_priorities.agendas.destroy');
    });
});
