<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\IpController;
use App\Http\Controllers\v1\RoleController;
use App\Http\Controllers\v1\SoapController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\VisitController;
use App\Http\Controllers\v1\IcdTenController;
use App\Http\Controllers\v1\RecipeController;
use App\Http\Controllers\v1\IcdNineController;
use App\Http\Controllers\v1\ProfileController;
use App\Http\Controllers\v1\TemplateController;
use App\Http\Controllers\v1\ActionLabController;
use App\Http\Controllers\v1\ActionRadController;
use App\Http\Controllers\v1\AssesmentController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\CommentLabController;
use App\Http\Controllers\v1\CommentRadController;
use App\Http\Controllers\v1\ConcoctionController;
use App\Http\Controllers\v1\MeasureRadController;
use App\Http\Controllers\v1\RehabMedicController;
use App\Http\Controllers\v1\RequestLabController;
use App\Http\Controllers\v1\RequestRadController;
use App\Http\Controllers\v1\SpecialistController;
use App\Http\Controllers\v1\ActionRehabController;
use App\Http\Controllers\v1\ActivityLogController;
use App\Http\Controllers\v1\CommentRehabController;
use App\Http\Controllers\v1\RequestRehabController;
use App\Http\Controllers\v1\SubAssesmentController;
use App\Http\Controllers\v1\CommentRecipeController;
use App\Http\Controllers\v1\NonConcoctionController;
use App\Http\Controllers\v1\SubMeasureRadController;
use App\Http\Controllers\v1\ActionRadAttchController;
use App\Http\Controllers\v1\AdultAssesmentController;
use App\Http\Controllers\v1\Auth\TwoFactorController;
use App\Http\Controllers\v1\Auth\VerifyEmailController;
use App\Http\Controllers\v1\ConcoctionMedicineController;
use App\Http\Controllers\v1\Auth\ForgotPasswordController;
use App\Http\Controllers\v1\IntegrationResultController;
use App\Http\Controllers\v1\NotificationController;
use App\Http\Controllers\v1\ResumeResultController;
use App\Models\IntegrationResult;
use Illuminate\Support\Facades\Broadcast;

Route::prefix('v1')->group(function () {
    Broadcast::routes(['middleware' => ['auth:sanctum']]);
    Route::get('list-ip', [IpController::class, 'listIp'])->name('list.ip');
    Route::middleware('active.ip')->group(function () {
        Route::post('login', [LoginController::class, 'login'])->name('login');

        Route::post('send-reset-link', [ForgotPasswordController::class, 'sendResetLink'])->name('send-reset-link');
        Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password');
        Route::get('check-token', [ForgotPasswordController::class, 'checkToken'])->name('check-token');

        Route::post('verify-email/{id}/{hash}', VerifyEmailController::class)->name('verify-email');


        Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
            Route::post('verify-two-factor', [TwoFactorController::class, 'store'])->name('verify-two-factor');
            Route::post('resend-two-factor', [TwoFactorController::class, 'resend'])->name('resend-two-factor');

            Route::middleware(['two.factor'])->group(function () {
                Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
                Route::get('profile/edit', [ProfileController::class, 'edit'])->name('edit');
                Route::patch('profile/update', [ProfileController::class, 'update'])->name('update');
                Route::post('logout', [LoginController::class, 'logout'])->name('logout');

                // KUNJUNGAN STATUS
                Route::prefix('visit')->group(function () {
                    Route::get('listing', [VisitController::class, 'listing'])->name('visit.listing');
                    Route::get('/{visitId}/edit', [VisitController::class, 'edit'])->name('visit.edit');
                    Route::patch('/{visitId}/update', [VisitController::class, 'update'])->name('visit.update');
                });

                //Notification
                Route::get('notification/listing', [NotificationController::class, 'listing'])->name('notification.listing');
                Route::delete('notification/{notificationId}/read', [NotificationController::class, 'readNotification'])->name('notification.readNotification');

                //ACL
                Route::prefix('role')->group(function () {
                    Route::get('/listing', [RoleController::class, 'listing'])->name('role.listing');
                    Route::get('/list-permission', [RoleController::class, 'listPermission'])->name('role.listing.permission');
                    Route::post('/store', [RoleController::class, 'store'])->name('role.store');
                    Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('role.edit');
                    Route::patch('/update/{role}', [RoleController::class, 'update'])->name('role.update');
                    Route::delete('/delete/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
                });

                //SPECIALIST
                Route::prefix('specialist')->group(function () {
                    Route::get('/listing', [SpecialistController::class, 'listing'])->name('specialist.listing');
                    Route::post('/store', [SpecialistController::class, 'store'])->name('specialist.store');
                    Route::get('/edit/{specialist}', [SpecialistController::class, 'edit'])->name('specialist.edit');
                    Route::patch('/update/{specialist}', [SpecialistController::class, 'update'])->name('specialist.update');
                    Route::delete('/delete/{specialist}', [SpecialistController::class, 'destroy'])->name('specialist.destroy');
                });

                //USER
                Route::prefix('user')->group(function () {
                    Route::get('/listing', [UserController::class, 'listing'])->name('user.listing');
                    Route::get('/listing-doctor', [UserController::class, 'listingDokter'])->name('user.listing.doctor');
                    Route::post('/store', [UserController::class, 'store'])->name('user.store');
                    Route::get('/list-role', [UserController::class, 'listingRole'])->name('user.listing.role');
                    Route::get('/list-specialist', [UserController::class, 'listingSpecialist'])->name('user.listing.specialist');
                    Route::get('/edit/{user}', [UserController::class, 'edit'])->name('user.edit')->where('user', '[0-9]+');
                    Route::post('/{user}/email-reset', [UserController::class, 'sendEmailReset'])->name('user.sendEmailReset');
                    Route::patch('/update/{user}', [UserController::class, 'update'])->name('user.update')->where('user', '[0-9]+');
                    Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy')->where('user', '[0-9]+');
                });

                // //SOAP
                // Route::prefix('soap')->group(function() {
                //     Route::get('/check-soap-number/{visitId}', [SoapController::class, 'checkSoapNumber'])->name('soap.number');
                //     Route::get('/listing/{visitId}', [SoapController::class, 'listing'])->name('soap.list');
                //     Route::get('/list-template', [SoapController::class, 'listingTemplate'])->name('soap.listTemplate');
                //     Route::post('/store/{visitId}', [SoapController::class, 'store'])->name('soap.store');

                //     // ASSESMENT
                //     Route::get('/{visitId}/{soap}/assesment/listing', [AssesmentController::class, 'listing'])->name('soap.edit')->where('soap', '[0-9]+');
                //     Route::post('/{visitId}/{soap}/assesment/store', [AssesmentController::class, 'store'])->name('assesment.store');
                //     Route::patch('/{visitId}/{soap}/assesment/update-status/{assesment}', [AssesmentController::class, 'updateStatus'])->name('assesment.updateStatus');
                //     Route::get('/{visitId}/{soap}/assesment/sub-dokumen/{assesment}', [AssesmentController::class, 'listSubDokumen'])->name('soap.listSubDokumen')->where('soap', '[0-9]+');
                //     Route::get('/{visitId}/{soap}/assesment/{assesment}/attribute', [AssesmentController::class, 'attribute'])->name('assesment.listAttribute');
                //     Route::patch('/{visitId}/{soap}/assesment/{assesment}', [AssesmentController::class, 'updateAssesment'])->name('assesment.updateAssesment');

                //     // COVID
                //     Route::post('{soap}/covid/store', [CovidAssesmentController::class, 'store'])->name('soap.covid.store');
                //     Route::get('{soap}/covid/list/{covidAssesment}', [CovidAssesmentController::class, 'listSubDokumen'])->name('soap.covid.list');
                //     Route::get('{soap}/covid/edit/{covidAssesment}', [CovidAssesmentController::class, 'edit'])->name('soap.covid.edit');
                //     Route::patch('{soap}/covid/update-status/{covidAssesment}', [CovidAssesmentController::class, 'updateStatus'])->name('soap.covid.updateStatus');
                //     Route::patch('{soap}/covid/update/{covidAssesment}', [CovidAssesmentController::class, 'update'])->name('soap.covid.update');
                //     // DEWASA
                //     Route::post('{soap}/adult/store', [AdultAssesmentController::class, 'store'])->name('soap.adult.store');
                //     Route::get('{soap}/adult/edit/{adultAssesment}', [AdultAssesmentController::class, 'edit'])->name('soap.adult.edit');
                //     Route::get('{soap}/adult/list/{adultAssesment}', [AdultAssesmentController::class, 'listSubDokumen'])->name('soap.adult.list');
                //     Route::patch('{soap}/adult/update-status/{adultAssesment}', [AdultAssesmentController::class, 'updateStatus'])->name('soap.adult.updateStatus');
                //     Route::patch('{soap}/adult/update/{adultAssesment}', [AdultAssesmentController::class, 'update'])->name('soap.adult.update');
                //     Route::get('{soap}/adult/inspection/listing/{patientId}', [AdultAssesmentController::class, 'listingInspection'])->name('soap.adult.create.inspection');
                //     Route::post('{soap}/adult/inspection/create/{patientId}', [AdultAssesmentController::class, 'createInspection'])->name('soap.adult.create.inspection');
                //     Route::get('{soap}/adult/inspection/edit/{adultInspection}', [AdultAssesmentController::class, 'editInspection'])->name('soap.adult.edit.inspection');
                //     Route::patch('{soap}/adult/inspection/update/{adultInspection}', [AdultAssesmentController::class, 'updateInspection'])->name('soap.adult.update.inspection');
                //     Route::delete('{soap}/adult/inspection/delete/{adultInspection}', [AdultAssesmentController::class, 'destroyInspection'])->name('soap.adult.destroy.inspection');
                //     Route::get('{soap}/adult/resume/listing/{patientId}', [AdultAssesmentController::class, 'listingResume'])->name('soap.adult.create.resume');
                //     Route::post('{soap}/adult/resume/create/{patientId}', [AdultAssesmentController::class, 'createResume'])->name('soap.adult.create.resume');
                //     Route::get('{soap}/adult/resume/edit/{adultResume}', [AdultAssesmentController::class, 'editResume'])->name('soap.adult.edit.resume');
                //     Route::patch('{soap}/adult/resume/update/{adultResume}', [AdultAssesmentController::class, 'updateResume'])->name('soap.adult.update.resume');
                //     Route::delete('{soap}/adult/resume/delete/{adultResume}', [AdultAssesmentController::class, 'destroyResume'])->name('soap.adult.destroy.resume');
                //     Route::get('/adult/allergy/edit/{patientId}', [AdultAssesmentController::class, 'editAllergy'])->name('soap.adult.edit.allergy');
                //     Route::patch('/adult/allergy/update/{patientId}', [AdultAssesmentController::class, 'updateAllergy'])->name('soap.adult.update.allergy');
                //     // ANAK
                //     Route::post('{soap}/kids/store', [KidsAssesmentsController::class, 'store'])->name('soap.kids.store');
                //     Route::get('{soap}/kids/edit/{kidsAssesment}', [KidsAssesmentsController::class, 'edit'])->name('soap.kids.edit');
                //     Route::get('{soap}/kids-assesment/edit/{kidsAssesment}', [KidsAssesmentsController::class, 'editKidsAssesment'])->name('soap.kids.edit.assesment');
                //     Route::get('{soap}/kids-poli/edit/{kidsAssesment}', [KidsAssesmentsController::class, 'editKidsPoli'])->name('soap.kids.edit.poli');
                //     Route::patch('{soap}/kids/update-status/{kidsAssesment}', [KidsAssesmentsController::class, 'updateStatus'])->name('soap.kids.updateStatus');
                //     Route::patch('{soap}/kids-assesment/update/{kidsAssesment}', [KidsAssesmentsController::class, 'update'])->name('soap.kids.update');
                //     Route::patch('{soap}/kids-poli/update/{kidsAssesment}', [KidsAssesmentsController::class, 'update'])->name('soap.kids.update');
                // });

                // TEMPLATE
                Route::prefix('template')->group(function () {
                    Route::get('/list-group', [TemplateController::class, 'listGroup'])->name('template.listGroup');
                    Route::get('/listing', [TemplateController::class, 'listing'])->name('template.listing');
                    Route::patch('/update-status/{template}', [TemplateController::class, 'updateStatus'])->name('template.updateStatus');
                    Route::get('/edit/{template}', [TemplateController::class, 'edit'])->name('template.edit');
                    Route::get('/list-attribute/{template}', [TemplateController::class, 'listingAttribute'])->name('template.listingAttribute');
                    Route::get('{template}/edit-attribute/{attribute}', [TemplateController::class, 'editAttribute'])->name('template.editAttribute');
                    Route::patch('/{template}/update-status-attribute/{attribute}', [TemplateController::class, 'updateStatusAttribute'])->name('template.attribute.updateStatus');
                    Route::patch('/{template}/update-attribute/{attribute}', [TemplateController::class, 'update'])->name('template.attribute.update');
                });

                //SOAP
                Route::prefix('visit')->group(function () {
                    Route::get('/{visitId}/soap/listing', [SoapController::class, 'listing'])->name('soap.list');
                    Route::get('/{visitId}/soap/list-template', [SoapController::class, 'listingTemplate'])->name('soap.listTemplate');
                    Route::get('/{visitId}/soap/check-button', [SoapController::class, 'checkButton'])->name('soap.checkButton');
                    Route::post('/{visitId}/soap/store', [SoapController::class, 'store'])->name('soap.store');
                    Route::get('/{visitId}/soap/print-pdf', [SoapController::class, 'printHistorySoap'])->name('soap.printHistorySoap');
                    Route::get('/{visitId}/soap/preview-pdf', [SoapController::class, 'previewHistorySoap'])->name('soap.printHistorySoap');
                    Route::post('/{visitId}/soap/store-global', [SoapController::class, 'storeGlobal'])->name('soap.storeGlobal');
                    Route::get('/{visitId}/soap/{soap}/edit', [SoapController::class, 'edit'])->name('soap.edit');

                    // ASSESMENT
                    Route::get('/{visitId}/soap/{soap}/assesment/listing', [AssesmentController::class, 'listing'])->name('soap.edit')->where('soap', '[0-9]+');
                    Route::post('/{visitId}/soap/{soap}/assesment/store', [AssesmentController::class, 'store'])->name('assesment.store');
                    Route::patch('/{visitId}/soap/{soap}/assesment/{assesment}/update-status', [AssesmentController::class, 'updateStatus'])->name('assesment.updateStatus');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/edit', [AssesmentController::class, 'edit'])->name('soap.listSubDokumen')->where('soap', '[0-9]+');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/author', [SubAssesmentController::class, 'viewAuthor'])->name('soap.viewAuthor')->where('soap', '[0-9]+');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen', [SubAssesmentController::class, 'listSubDokumen'])->name('soap.listSubDokumen')->where('soap', '[0-9]+');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/attribute', [SubAssesmentController::class, 'attribute'])->name('subAssesment.listAttribute');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/edit-sub-dokumen', [SubAssesmentController::class, 'edit'])->name('subAssesment.edit-sub-assesment');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/edit', [SubAssesmentController::class, 'editAssesment'])->name('subAssesment.edit');
                    Route::patch('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/update', [SubAssesmentController::class, 'updateAssesment'])->name('subAssesment.update');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/printPdf', [SubAssesmentController::class, 'printPDF'])->name('subAssesment.printPDF');
                    Route::get('/{visitId}/soap/{soap}/assesment/{assesment}/sub-dokumen/{subAssesment}/printPdf', [SubAssesmentController::class, 'printPdfSubAssesment'])->name('subAssesment.printPdfSubAssesment');
                    // Route::get('/{visitId}/printPdfInspection', [SubAssesmentController::class, 'printPdfInspection'])->name('subAssesment.printPdfInspection');
                    // Route::get('/{visitId}/printPdfResume', [SubAssesmentController::class, 'printPdfResume'])->name('subAssesment.printPdfResume');

                    // // HASIL PEMERIKSAAN DAN RESUME RAWAT JALAN
                    // Route::get('/{visitId}/soap/{soap}/adult/inspection/listing', [SubAssesmentController::class, 'listingInspection'])->name('soap.adult.create.inspection');
                    // Route::post('/{visitId}/soap/{soap}/adult/inspection/create', [SubAssesmentController::class, 'createInspection'])->name('soap.adult.create.inspection');
                    // Route::get('soap/{soap}/adult/inspection/edit/{adultInspection}', [SubAssesmentController::class, 'editInspection'])->name('soap.adult.edit.inspection');
                    // Route::patch('soap/{soap}/adult/inspection/update/{adultInspection}', [SubAssesmentController::class, 'updateInspection'])->name('soap.adult.update.inspection');
                    // Route::delete('soap/{soap}/adult/inspection/delete/{adultInspection}', [SubAssesmentController::class, 'destroyInspection'])->name('soap.adult.destroy.inspection');
                    // Route::get('/{visitId}/soap/{soap}/adult/resume/listing', [SubAssesmentController::class, 'listingResume'])->name('soap.adult.create.resume');
                    // Route::post('/{visitId}/soap/{soap}/adult/resume/create', [SubAssesmentController::class, 'createResume'])->name('soap.adult.create.resume');
                    // Route::get('soap/{soap}/adult/resume/edit/{adultResume}', [SubAssesmentController::class, 'editResume'])->name('soap.adult.edit.resume');
                    // Route::patch('soap/{soap}/adult/resume/update/{adultResume}', [SubAssesmentController::class, 'updateResume'])->name('soap.adult.update.resume');
                    // Route::delete('soap/{soap}/adult/resume/delete/{adultResume}', [SubAssesmentController::class, 'destroyResume'])->name('soap.adult.destroy.resume');
                    // Route::get('/{visitId}/soap/adult/allergy/edit', [SubAssesmentController::class, 'editAllergy'])->name('soap.adult.edit.allergy');
                    // Route::patch('/{visitId}/soap/adult/allergy/update', [SubAssesmentController::class, 'updateAllergy'])->name('soap.adult.update.allergy');
                });

                //Activity Log
                Route::prefix('activity-log')->group(function () {
                    Route::get('/listing', [ActivityLogController::class, 'listing'])->name('log.listing');
                });

                //Laboratorium
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/laboratorium/check-is-read', [RequestLabController::class, 'checkIsRead'])->name('lab.checkIsRead');
                    Route::get('{visitId}/laboratorium/listing', [RequestLabController::class, 'listingLaboratoriumUser'])->name('lab.listing.user');
                    Route::get('laboratorium/listing', [RequestLabController::class, 'listingLaboratorium'])->name('lab.listing.laboratorium');
                    Route::get('laboratorium/listingResult', [RequestLabController::class, 'listingResultLaboratorium'])->name('lab.listing.resultLaboratorium');
                    Route::get('{visitId}/laboratorium/listing-for-assesment', [RequestLabController::class, 'listingLaboratoriumAssesment'])->name('lab.listing.forAssesment');
                    Route::post('{visitId}/laboratorium/store', [RequestLabController::class, 'store'])->name('lab.store');
                    Route::get('laboratorium/{requestLab}/edit', [RequestLabController::class, 'edit'])->name('lab.edit');
                    Route::patch('laboratorium/{requestLab}/update-status', [RequestLabController::class, 'updateStatusRequest'])->name('lab.updateStatus');
                    Route::get('laboratorium/result', [RequestLabController::class, 'resultRequestLab'])->name('lab.result');
                    Route::get('/{visitId}/laboratorium/print-pdf', [RequestLabController::class, 'printHistoryLab'])->name('lab.printHistoryLab');
                    Route::get('/{visitId}/laboratorium/preview-pdf', [RequestLabController::class, 'previewHistoryLab'])->name('lab.printHistoryLab');

                    //Tindakan
                    Route::get('laboratorium/{requestLab}/measure/listing', [ActionLabController::class, 'listingMeasure'])->name('lab.measure');
                    Route::get('laboratorium/{requestLab}/measure/listing-update', [ActionLabController::class, 'listingMeasureForUpdate'])->name('lab.listingMeasureForUpdate');
                    Route::patch('laboratorium/{requestLab}/measure/{actionLab}/update-status', [ActionLabController::class, 'updateStatusMeasure'])->name('lab.measure.updateStatus');
                    Route::patch('laboratorium/{requestLab}/measure/{actionLab}/update-order', [ActionLabController::class, 'updateOrderMeasure'])->name('lab.measure.updateOrder');
                    Route::post('laboratorium/{requestLab}/measure/store', [ActionLabController::class, 'storeMeasure'])->name('lab.measure.store');
                    Route::delete('laboratorium/{requestLab}/measure/{actionLab}/destroy', [ActionLabController::class, 'deleteMeasure'])->name('lab.measure.deleteMeasure');

                    //Comment
                    Route::get('laboratorium/{requestLab}/comment', [CommentLabController::class, 'showComment'])->name('lab.comment');
                    Route::post('laboratorium/{requestLab}/store-comment', [CommentLabController::class, 'storeComment'])->name('lab.store-comment');
                });

                //Data Master Tindakan Radiologi
                Route::prefix('master')->group(function () {
                    Route::prefix('/radiology')->group(function () {
                        // Group
                        Route::prefix('/group')->group(function () {
                            Route::get('/listing', [MeasureRadController::class, 'listing'])->name('master.listing');
                            Route::post('/store', [MeasureRadController::class, 'store'])->name('master.store');
                            Route::get('/{measureRad}/edit', [MeasureRadController::class, 'edit'])->name('master.edit');
                            Route::patch('/{measureRad}/update', [MeasureRadController::class, 'update'])->name('master.update');
                            Route::post('/{measureRad}/delete', [MeasureRadController::class, 'destroy'])->name('master.destroy');
                        });

                        //Sub Group / Tindakan
                        Route::prefix('/tindakan')->group(function () {
                            Route::get('/listing', [SubMeasureRadController::class, 'listing'])->name('master.sub-listing');
                            Route::get('/listing-group', [SubMeasureRadController::class, 'listingGroup'])->name('master.sub-listing-group');
                            Route::post('/store', [SubMeasureRadController::class, 'store'])->name('master.sub-store');
                            Route::get('/{subMeasureRad}/edit', [SubMeasureRadController::class, 'edit'])->name('master.sub-edit');
                            Route::get('/{subMeasureRad}/edit-group', [SubMeasureRadController::class, 'editGroup'])->name('master.sub-edit-group');
                            Route::patch('/{subMeasureRad}/update', [SubMeasureRadController::class, 'update'])->name('master.sub-update');
                            Route::delete('/{subMeasureRad}/delete', [SubMeasureRadController::class, 'destroy'])->name('master.sub-destroy');
                        });
                    });
                });

                //Radiologi
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/radiology/listing', [RequestRadController::class, 'listingRadiologyUser'])->name('rad.listing.user');
                    Route::get('radiology/listing', [RequestRadController::class, 'listingRadiology'])->name('rad.listing.radiologi');
                    Route::get('radiology/listing-group', [RequestRadController::class, 'listingGroupRadiology'])->name('rad.listing.group.radiologi');
                    Route::post('{visitId}/radiology/store', [RequestRadController::class, 'store'])->name('rad.store');
                    Route::get('radiology/{requestRad}/edit', [RequestRadController::class, 'edit'])->name('rad.edit');
                    Route::patch('radiology/{requestRad}/update-status', [RequestRadController::class, 'updateStatusRequest'])->name('rad.updateStatus');
                    Route::get('radiology/{requestRad}/print-pdf', [RequestRadController::class, 'printPDF'])->name('rad.printPDF');
                    Route::get('radiology/{requestRad}/preview-pdf', [RequestRadController::class, 'previewPDF'])->name('rad.previewPDF');
                    Route::get('radiology/measure/listing', [RequestRadController::class, 'listingAllMeasure'])->name('rad.listingAllMeasure');
                    Route::get('/{visitId}/radiology/print-pdf', [RequestRadController::class, 'printHistoryRad'])->name('rad.printHistoryRad');
                    Route::get('/{visitId}/radiology/preview-pdf', [RequestRadController::class, 'previewHistoryRad'])->name('rad.printHistoryRad');

                    //Tindakan
                    Route::get('radiology/{requestRad}/measure/listing', [ActionRadController::class, 'listingMeasure'])->name('rad.measure');
                    Route::get('radiology/{requestRad}/measure/listing-update', [ActionRadController::class, 'listingMeasureForUpdate'])->name('rad.listingMeasureForUpdate');
                    Route::get('radiology/{requestRad}/measure/{actionRad}/edit', [ActionRadController::class, 'edit'])->name('rad.measure.edit');
                    Route::patch('radiology/{requestRad}/measure/{actionRad}/update-status', [ActionRadController::class, 'updateStatusMeasure'])->name('rad.measure.updateStatus');
                    Route::patch('radiology/{requestRad}/measure/{actionRad}/update-order', [ActionRadController::class, 'updateOrderMeasure'])->name('rad.measure.updateOrder');
                    Route::patch('radiology/{requestRad}/measure/{actionRad}/update-result', [ActionRadController::class, 'updateResultMeasure'])->name('rad.measure.updateResult');
                    Route::post('radiology/{requestRad}/measure/store', [ActionRadController::class, 'storeMeasure'])->name('rad.measure.store');
                    Route::delete('radiology/{requestRad}/measure/{actionRad}/destroy', [ActionRadController::class, 'deleteMeasure'])->name('rad.measure.deleteMeasure');

                    //Attachment Tindakan Radiologi
                    Route::get('radiology/{requestRad}/measure/{actionRad}/listing-attachment', [ActionRadAttchController::class, 'listing'])->name('rad.attach.listing');
                    Route::post('radiology/{requestRad}/measure/{actionRad}/store-attachment', [ActionRadAttchController::class, 'store'])->name('rad.attach.store');
                    Route::get('radiology/measure/attachment/{actionRadAttch}/edit-attachment', [ActionRadAttchController::class, 'edit'])->name('rad.attach.edit');
                    Route::patch('radiology/measure/attachment/{actionRadAttch}/update-attachment', [ActionRadAttchController::class, 'update'])->name('rad.attach.update');
                    Route::delete('radiology/measure/{actionRad}/attachment/{actionRadAttch}/delete-attachment', [ActionRadAttchController::class, 'destroy'])->name('rad.attach.destroy');

                    //Comment
                    Route::get('radiology/{requestRad}/comment', [CommentRadController::class, 'showComment'])->name('rad.comment');
                    Route::post('radiology/{requestRad}/store-comment', [CommentRadController::class, 'storeComment'])->name('rad.store-comment');
                });

                //E-Resep
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/recipe/listing', [RecipeController::class, 'listingRecipeUser'])->name('recipe.listing.user');
                    Route::get('recipe/listing', [RecipeController::class, 'listingRecipe'])->name('recipe.listing.apoteker');
                    Route::get('recipe/listing-resep', [RecipeController::class, 'listingStoreRecipe'])->name('recipe.listing.store');
                    Route::post('{visitId}/recipe/store', [RecipeController::class, 'storeRecipe'])->name('recipe.store');
                    Route::patch('recipe/{recipe}/update-status', [RecipeController::class, 'updateStatusRecipe'])->name('recipe.updateStatus');
                    Route::get('recipe/{recipe}/edit', [RecipeController::class, 'editRecipe'])->name('recipe.edit');
                    Route::get('recipe/{recipe}/print-pdf', [RecipeController::class, 'printPDF'])->name('recipe.printPDF');
                    Route::get('recipe/{recipe}/preview-pdf', [RecipeController::class, 'previewPDF'])->name('recipe.previewPDF');
                    Route::get('/{visitId}/recipe/print-pdf', [RecipeController::class, 'printHistoryRecipe'])->name('recipe.printHistoryRecipe');
                    Route::get('/{visitId}/recipe/preview-pdf', [RecipeController::class, 'previewHistoryRecipe'])->name('recipe.printHistoryRecipe');

                    //Racikan
                    Route::get('recipe/{recipe}/concoction/listing', [ConcoctionController::class, 'listing'])->name('recipe.concoction.listing');
                    Route::get('recipe/{recipe}/concoction/{concoction}/edit', [ConcoctionController::class, 'edit'])->name('recipe.concoction.edit');
                    Route::post('recipe/{recipe}/concoction/store', [ConcoctionController::class, 'store'])->name('recipe.concoction.store');
                    Route::patch('recipe/{recipe}/concoction/{concoction}/update', [ConcoctionController::class, 'update'])->name('recipe.concoction.update');
                    Route::delete('recipe/{recipe}/concoction/{concoction}/destroy', [ConcoctionController::class, 'destroy'])->name('recipe.concoction.destroy');
                    Route::get('recipe/concoction/{concoction}/listing-medicine', [ConcoctionMedicineController::class, 'listing'])->name('recipe.concoction.listing-medicine');
                    Route::post('recipe/{recipe}/concoction/{concoction}/store', [ConcoctionMedicineController::class, 'store'])->name('recipe.concoction.store');
                    Route::get('recipe/concoction/medicine/{concoctionMedicine}/edit', [ConcoctionMedicineController::class, 'edit'])->name('recipe.concoction.edit');
                    Route::patch('recipe/{recipe}/concoction/{concoction}/medicine/{concoctionMedicine}/update', [ConcoctionMedicineController::class, 'update'])->name('recipe.concoction.update');
                    Route::delete('recipe/{recipe}/concoction/{concoction}/medicine/{concoctionMedicine}/destroy', [ConcoctionMedicineController::class, 'destroy'])->name('recipe.concoction.destroy');

                    //Comment
                    Route::get('recipe/{recipe}/comment', [CommentRecipeController::class, 'showComment'])->name('recipe.comment');
                    Route::post('recipe/{recipe}/store-comment', [CommentRecipeController::class, 'storeComment'])->name('recipe.store-comment');

                    //Non Racikan
                    Route::get('recipe/{recipe}/non-concoction/listing', [NonConcoctionController::class, 'listing'])->name('recipe.non-concoction.listing');
                    Route::post('recipe/{recipe}/non-concoction/store', [NonConcoctionController::class, 'store'])->name('recipe.non-concoction.store');
                    Route::get('recipe/non-concoction/{nonConcoction}/edit', [NonConcoctionController::class, 'edit'])->name('recipe.non-concoction.edit');
                    Route::patch('recipe/non-concoction/{nonConcoction}/update', [NonConcoctionController::class, 'update'])->name('recipe.non-concoction.update');
                    Route::delete('recipe/{recipe}/non-concoction/{nonConcoction}/destroy', [NonConcoctionController::class, 'destroy'])->name('recipe.non-concoction.destroy');
                });

                // Old Rehab Medic
                // Route::prefix('visit')->group(function () {
                //     Route::get('{visitId}/rehab-medic/listing', [RehabMedicController::class, 'listing'])->name('rehab-medic.listing');
                //     Route::post('{visitId}/rehab-medic/store', [RehabMedicController::class, 'store'])->name('rehab-medic.store');
                //     Route::patch('rehab-medic/{rehabMedic}/update-status', [RehabMedicController::class, 'updateStatus'])->name('rehab-medic.updateStatus');
                // });

                // Rehab Medic
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/rehab/listing', [RequestRehabController::class, 'listingRehabUser'])->name('reh.listing.user');
                    Route::get('rehab/listing', [RequestRehabController::class, 'listingRehab'])->name('reh.listing.rehab');
                    Route::post('{visitId}/rehab/store', [RequestRehabController::class, 'store'])->name('reh.store');
                    Route::get('rehab/{requestRehab}/edit', [RequestRehabController::class, 'edit'])->name('reh.edit');
                    Route::patch('rehab/{requestRehab}/update-status', [RequestRehabController::class, 'updateStatusRehab'])->name('reh.updateStatus');
                    Route::get('rehab/{requestRehab}/print-pdf', [RequestRehabController::class, 'printPDF'])->name('rehab.printPDF');
                    Route::get('rehab/{requestRehab}/preview-pdf', [RequestRehabController::class, 'previewPDF'])->name('rehab.previewPDF');
                    Route::get('/{visitId}/rehab/print-pdf', [RequestRehabController::class, 'printHistoryRehab'])->name('rehab.printHistoryRehab');
                    Route::get('/{visitId}/rehab/preview-pdf', [RequestRehabController::class, 'previewHistoryRehab'])->name('rehab.printHistoryRehab');

                    //Tindakan
                    Route::get('rehab/{requestRehab}/measure/listing', [ActionRehabController::class, 'listingMeasure'])->name('reh.measure');
                    Route::get('rehab/{requestRehab}/measure/listing-update', [ActionRehabController::class, 'listingMeasureForUpdate'])->name('reh.listingMeasureForUpdate');
                    Route::patch('rehab/{requestRehab}/measure/{actionRehab}/update-status', [ActionRehabController::class, 'updateStatusMeasure'])->name('reh.measure.updateStatus');
                    Route::patch('rehab/{requestRehab}/measure/{actionRehab}/update-order', [ActionRehabController::class, 'updateOrderMeasure'])->name('reh.measure.updateOrder');
                    Route::post('rehab/{requestRehab}/measure/store', [ActionRehabController::class, 'storeMeasure'])->name('reh.measure.store');
                    Route::delete('rehab/{requestRehab}/measure/{actionRehab}/destroy', [ActionRehabController::class, 'deleteMeasure'])->name('reh.measure.deleteMeasure');

                    //Comment
                    Route::get('rehab/{requestRehab}/comment', [CommentRehabController::class, 'showComment'])->name('reh.comment');
                    Route::post('rehab/{requestRehab}/store-comment', [CommentRehabController::class, 'storeComment'])->name('reh.store-comment');
                });

                // Hasil Terintegrasi 
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/integration-result/listing', [IntegrationResultController::class, 'listing'])->name('integration.result.listing');
                    Route::post('{visitId}/integration-result/store', [IntegrationResultController::class, 'store'])->name('integration.result.store');
                    Route::get('integration-result/{integrationResult}/edit', [IntegrationResultController::class, 'edit'])->name('integration.result.edit');
                    Route::patch('integration-result/{integrationResult}/update', [IntegrationResultController::class, 'update'])->name('integration.result.update');
                    Route::patch('integration-result/{integrationResult}/update-status', [IntegrationResultController::class, 'updateStatus'])->name('integration.result.updateStatus');
                    Route::get('/{visitId}/integration-result/print-pdf', [IntegrationResultController::class, 'printPdfAll'])->name('integration.perintPdfAll');
                    Route::get('integration-result/{integrationResult}/print-pdf', [IntegrationResultController::class, 'printPdf'])->name('integration.perintPdf');
                });

                // Hasil Resume
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/resume-result/listing', [ResumeResultController::class, 'listing'])->name('resume.result.listing');
                    Route::get('resume-result/{resumeResult}/edit', [ResumeResultController::class, 'edit'])->name('resume.result.edit');
                    Route::patch('resume-result/{resumeResult}/update', [ResumeResultController::class, 'update'])->name('resume.result.update');
                    Route::get('/{visitId}/resume-result/print-pdf', [ResumeResultController::class, 'printPdf'])->name('resume.perintPdf');
                    Route::get('/{visitId}/resume-result/preview-pdf', [ResumeResultController::class, 'previewPdf'])->name('resume.previewPdf');
                });

                //Tindakan ICD
                Route::prefix('visit')->group(function () {
                    Route::get('{visitId}/icd/listing-assesment', [IcdNineController::class, 'listingAssesment'])->name('icd.listingAssesment');
                    Route::get('/{visitId}/icd/print-pdf', [IcdNineController::class, 'printHistoryIcd'])->name('icd.printHistoryIcd');
                    Route::get('/{visitId}/icd/preview-pdf', [IcdNineController::class, 'previewHistoryIcd'])->name('icd.previewHistoryIcd');

                    // ICD 9
                    Route::get('{visitId}/icd-nine/listing', [IcdNineController::class, 'listing'])->name('icd.nine.listing');
                    Route::get('icd-nine/listing-dropdown', [IcdNineController::class, 'listingDropdownFiller'])->name('icd.nine.listing-dropdown');
                    Route::post('{visitId}/icd-nine/store', [IcdNineController::class, 'store'])->name('icd.nine.store');
                    Route::get('icd-nine/{icdNine}/edit', [IcdNineController::class, 'edit'])->name('icd.nine.edit');
                    Route::patch('icd-nine/{icdNine}/update', [IcdNineController::class, 'update'])->name('icd.nine.update');
                    Route::delete('icd-nine/{icdNine}/update-status', [IcdNineController::class, 'updateStatus'])->name('icd.nine.updateStatus');

                    //ICD 10
                    Route::get('{visitId}/icd-ten/listing', [IcdTenController::class, 'listing'])->name('icd.ten.listing');
                    Route::get('icd-ten/listing-dropdown', [IcdTenController::class, 'listingDropdownFiller'])->name('icd.ten.listing-dropdown');
                    Route::post('{visitId}/icd-ten/store', [IcdTenController::class, 'store'])->name('icd.ten.store');
                    Route::get('icd-ten/{icdTen}/edit', [IcdTenController::class, 'edit'])->name('icd.ten.edit');
                    Route::patch('icd-ten/{icdTen}/update', [IcdTenController::class, 'update'])->name('icd.ten.update');
                    Route::delete('icd-ten/{icdTen}/update-status', [IcdTenController::class, 'updateStatus'])->name('icd.ten.updateStatus');
                });
            });
        });
    });
});
