<?php

use App\Events\NotificationEvent;
use App\Http\Controllers\AccountInformationController;
use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\Admin\AdminContractHeaderController;
use App\Http\Controllers\Admin\AdminContractSubsetController;
use App\Http\Controllers\Admin\AdvantageController;
use App\Http\Controllers\Admin\CompanyInformationController;
use App\Http\Controllers\Admin\DefaultTableAttributeController;
use App\Http\Controllers\Admin\InvoiceCoverTitleController;
use App\Http\Controllers\Admin\MenuActionController;
use App\Http\Controllers\Admin\MenuHeaderController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\AdvantageAutomationController;
use App\Http\Controllers\AdvantageFormController;
use App\Http\Controllers\EmailResetPasswordController;
use App\Http\Controllers\ResetPasswordController;
use \App\Http\Controllers\SmsResetPasswordController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TableAttributeController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AxiosController;
use App\Http\Controllers\ContractHeaderController;
use App\Http\Controllers\ContractSubsetController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceAutomationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PerformanceAutomationController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\UserPasswordResetController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;
use \App\Http\Controllers\Admin\AutomationFlowController;
use \Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route("login");
});

Auth::routes(["register" => false,"reset" => false,"confirm" => false]);
Route::group(['prefix' => 'Password'],function () {
    Route::group(['prefix' => 'Reset'],function () {
        Route::get("/",[ResetPasswordController::class,"showForm"])->name("password.reset");
        Route::post("/SendLink",[ResetPasswordController::class,"sendResetLink"])->name("password.reset.send.link");
        Route::get("/ViaSms/{mobile}/{token}", [SmsResetPasswordController::class, "reset_form"])->name("password.reset.sms");
        Route::post("/ViaSms", [SmsResetPasswordController::class, "reset"])->name("password.update.sms");
        Route::get("/ViaEmail/{email}/{token}", [EmailResetPasswordController::class, "reset_form"])->name("password.reset.email");
        Route::post("/ViaEmail", [EmailResetPasswordController::class, "reset"])->name("password.update.email");
    });
    Route::group(['prefix' => 'UserReset','middleware' => ['auth']],function () {
        Route::get("/",[UserPasswordResetController::class,"show"])->name("user.password.reset");
        Route::put("/Change",[UserPasswordResetController::class,"change"])->name("user.password.reset.change");
    });
});

Route::group(['prefix' => 'Account','middleware' => ['auth']],function () {
    Route::group(['prefix' => 'Information'],function (){
        Route::get("/",[AccountInformationController::class,"show"])->name("account.information");
        Route::put("/Update",[AccountInformationController::class,"update"])->name("account.information.update");
    });
    Route::group(['prefix' => 'Verification'],function (){
        Route::get("/", [AccountVerificationController::class, "show"])->name("account.verification");
        Route::post("/SendLink", [AccountVerificationController::class, "send"])->name("account.verification.send");
        Route::get("/Mobile/{mobile}/{token}", [AccountVerificationController::class, "verify_mobile"])->name("account.verification.mobile");
        Route::get("/Email/{email}/{token}", [AccountVerificationController::class, "verify_email"])->name("account.verification.email");
    });
});

Route::post("/recaptcha",function (){
    return response()->json([
        'captcha' => Captcha::img()
    ]);
});

Route::group(['middleware' => ['auth']],function (){
    Route::post('/push',[PushController::class,"store"]);
});

Route::group(['middleware' => ['auth','staff_permission']],function (){
    Route::group(['prefix' => 'Employees'],function (){
        Route::post("/Edit",[AxiosController::class,"EmployeeEdit"])->name("EmployeeEdit");
        Route::post("/Delete",[AxiosController::class,"EmployeeDelete"])->name("EmployeeDelete");
        Route::post("/DeleteAll",[AxiosController::class,"EmployeeDeleteAll"])->name("EmployeeDeleteAll");
        Route::post("/Add",[AxiosController::class,"EmployeeAdd"])->name("EmployeeAdd");
        Route::post("/AddAll",[AxiosController::class,"EmployeeAddAll"])->name("EmployeeAddAll");
        Route::post("/Activation",[AxiosController::class,"EmployeeActivation"])->name("EmployeeActivation");
        Route::post("/EmployeeChangeContract",[AxiosController::class,"EmployeeChangeContract"])->name("EmployeeChangeContract");
        Route::post("/Search",[AxiosController::class,"EmployeeSearch"])->name("EmployeeSearch");
    });
    Route::group(['prefix' => "Performances"],function (){
        Route::post("/PreImport",[AxiosController::class,"PerformancePreImport"])->name("PerformancePreImport");
    });
    Route::group(['prefix' => "PerformanceAutomation"],function (){
        Route::post("/Import",[AxiosController::class,"PerformanceAutomationImport"])->name("PerformanceAutomationImport");
    });
    Route::group(['prefix' => "Invoices"],function (){
        Route::post("/PreImport",[AxiosController::class,"InvoicePreImport"])->name("InvoicePreImport");
    });
    Route::post("/NewAutomationData",[AxiosController::class,"NewAutomationData"])->name("NewAutomationData");
});

Route::group(['prefix'=>'Dashboard', 'middleware'=>['auth']],function() {

    Route::get("/",[DashboardController::class,"idle"])->name("idle");
    Route::group(['prefix'=>'Admin', 'middleware'=>['admin_permission']],function(){
        Route::get('/link-storage', function () {
            Artisan::call('storage:link');
        })->name("link_storage");
        Route::get('/serve-websocket', function(){
            Artisan::call('websockets:serve --port=6002');
        })->name("serve_websocket");
        Route::get('/clear', function () {
            Artisan::call('optimize:clear');
        })->name("clear_cache");
        Route::get('/run-schedule', function(){
            Artisan::call('schedule:run');
        })->name("run_schedule");

        Route::get("/",[DashboardController::class,"admin"])->name("admin_idle");
        Route::get("/CompanyInformation",[CompanyInformationController::class,"index"])->name("CompanyInformation.index");
        Route::put("/CompanyInformation",[CompanyInformationController::class,"update"])->name("CompanyInformation.update");
        Route::resource("/MenuHeaders",MenuHeaderController::class);
        Route::post("/MenuHeaders/Activation/{id}",[MenuHeaderController::class,"status"])->name("MenuHeaders.activation");
        Route::resource("/MenuItems",MenuItemController::class);
        Route::resource("/MenuActions",MenuActionController::class);
        Route::resource("/Users",UsersController::class);
        Route::post("/Users/Activation/{id}",[UsersController::class,"status"])->name("Users.activation");
        Route::get("/Users/Report/{id}",[UsersController::class,"report"])->name("Users.report");
        Route::delete("/Users/Report/{id}",[UsersController::class,"delete_report"])->name("Users.report.delete");
        Route::resource("/Roles",RoleController::class);
        Route::post("/Roles/Activation/{id}",[RoleController::class,"status"])->name("Roles.activation");
        Route::resource("/AutomationFlow",AutomationFlowController::class);
        Route::post("/AutomationFlow/Activation/{id}",[AutomationFlowController::class,"status"])->name("AutomationFlow.activation");
        Route::resource("/TableAttributes",TableAttributeController::class);
        Route::resource("/InvoiceCoverTitles",InvoiceCoverTitleController::class);
        Route::resource("/Advantages",AdvantageController::class);
        Route::post("/Advantages/Activation/{id}",[AdvantageController::class,"status"])->name("Advantages.activation");
        Route::resource("/DefaultTableAttributes",DefaultTableAttributeController::class);
        Route::resource("/AdminContractHeader",AdminContractHeaderController::class);
        Route::post("/ContractHeader/Activation/{id}",[AdminContractHeaderController::class,"status"])->name("AdminContractHeader.activation");
        Route::get("/ContractHeader/Download/{id}",[AdminContractHeaderController::class,"download_docs"])->name("AdminContractHeader.download");
        Route::resource("/AdminContractSubset",AdminContractSubsetController::class);
        Route::post("/AdminContractSubset/Activation/{id}",[AdminContractSubsetController::class,"status"])->name("AdminContractSubset.activation");
        Route::get("/AdminContractSubset/Download/{id}",[AdminContractSubsetController::class,"download_docs"])->name("AdminContractSubset.download");
    });


    Route::group(['prefix'=>'Staff', 'middleware'=>['staff_permission']],function(){
        Route::get("/",[DashboardController::class,"staff"])->name("staff_idle");

        Route::resource("/ContractHeader",ContractHeaderController::class);
        Route::post("/ContractHeader/Activation/{id}",[ContractHeaderController::class,"status"])->name("ContractHeader.activation");
        Route::get("/ContractHeader/Download/{id}",[ContractHeaderController::class,"download_docs"])->name("ContractHeader.download");

        Route::resource("/ContractSubset",ContractSubsetController::class);
        Route::post("/ContractSubset/Activation/{id}",[ContractSubsetController::class,"status"])->name("ContractSubset.activation");
        Route::get("/ContractSubset/Download/{id}",[ContractSubsetController::class,"download_docs"])->name("ContractSubset.download");
        Route::get("/ContractSubset/Excel/Export/Performance/Attributes",[ContractSubsetController::class,"performance_attributes_export_excel"])->name("ContractSubset.performance_attributes_export_excel");
        Route::get("/ContractSubset/Excel/Export/Invoice/Attributes",[ContractSubsetController::class,"invoice_attributes_export_excel"])->name("ContractSubset.invoice_attributes_export_excel");

        Route::resource("/StaffUsers",StaffUserController::class);
        Route::post("/StaffUsers/Activation/{id}",[StaffUserController::class,"status"])->name("StaffUsers.activation");

        Route::resource("/Employees",EmployeeController::class);
        Route::get("/Employees/New/Export",[EmployeeController::class,"export_new_employee_excel"])->name("Employees.export_new_employee_excel");
        Route::get("/Employees/ChangeContract/Export",[EmployeeController::class,"export_change_contract_employee_excel"])->name("Employees.export_change_contract_employee_excel");

        Route::resource("/Performances",PerformanceController::class);
        Route::post("/Performances/Confirm/{id}",[PerformanceController::class,"confirm"])->name("Performances.confirm");
        Route::get("/Performances/Excel/Export/{id}/{authorized_date_id?}",[PerformanceController::class,"performance_export_excel"])->name("Performances.performance_export_excel");

        Route::group(['prefix' => 'PerformanceAutomation'],function (){
            Route::get("/Index",[PerformanceAutomationController::class,'index'])->name("PerformanceAutomation.index");
            Route::get("/Details/{id}/{outbox?}",[PerformanceAutomationController::class,'details'])->name("PerformanceAutomation.details");
            Route::put("/Agree/{id}",[PerformanceAutomationController::class,'agree'])->name("PerformanceAutomation.agree");
            Route::put("/DisAgree/{id}",[PerformanceAutomationController::class,'disagree'])->name("PerformanceAutomation.disagree");
            Route::get("/Excel/Export/{id}/{authorized_date_id?}",[PerformanceAutomationController::class,"performance_export_excel"])->name("PerformanceAutomation.performance_export_excel");
        });

        Route::resource("/Invoices",InvoiceController::class);
        Route::post("/Invoices/Confirm/{id}",[InvoiceController::class,"confirm"])->name("Invoices.confirm");
        Route::get("/Invoices/Excel/Export/{id}/{authorized_date_id?}",[InvoiceController::class,"invoice_export_excel"])->name("Invoices.invoice_export_excel");

        Route::group(['prefix' => 'InvoiceAutomation'],function (){
            Route::get("/Index",[InvoiceAutomationController::class,'index'])->name("InvoiceAutomation.index");
            Route::get("/Details/{id}/{outbox?}",[InvoiceAutomationController::class,'details'])->name("InvoiceAutomation.details");
            Route::put("/Agree/{id}",[InvoiceAutomationController::class,'agree'])->name("InvoiceAutomation.agree");
            Route::put("/DisAgree/{id}",[InvoiceAutomationController::class,'disagree'])->name("InvoiceAutomation.disagree");
            Route::get("/Excel/Export/{id}/{automation_id?}",[InvoiceAutomationController::class,"invoice_export_excel"])->name("InvoiceAutomation.Invoice_export_excel");
            Route::get("/PrintCover/{id}",[InvoiceAutomationController::class,"print_cover"])->name("InvoiceAutomation.print_cover");
        });

        Route::resource("AdvantageForms", AdvantageFormController::class);
        Route::post("AdvantageForms/Confirm/{id}", [AdvantageFormController::class, "confirm"])->name("AdvantageForms.confirm");
        Route::get("AdvantageForms/AttachedFiles/{id}/{slug}",[AdvantageFormController::class,"attached_files"])->name("AdvantageForms.files");
        Route::get("AdvantageForms/AttachedFile/{id}/{filename}/",[AdvantageFormController::class,"attached_file"])->name("AdvantageForms.file");

        Route::group(['prefix' => 'AdvantageAutomation'],function (){
            Route::get("/Index",[AdvantageAutomationController::class,'index'])->name("AdvantageAutomation.index");
            Route::get("/Details/{id}",[AdvantageAutomationController::class,'details'])->name("AdvantageAutomation.details");
            Route::put("/Agree/{id}",[AdvantageAutomationController::class,'agree'])->name("AdvantageAutomation.agree");
            Route::put("/Disagree/{id}",[AdvantageAutomationController::class,'disagree'])->name("AdvantageAutomation.disagree");
        });

    });
});
