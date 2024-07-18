<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Models\Appointments;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('register',[AuthController::class,'register']);
// Route::post('login',[AuthController::class,'login']);

// Route::group([
//     "middleware" => ["auth:api"]
// ], function(){

//     Route::get("profile", [AuthController::class, "profile"]);
//     Route::get("refresh", [AuthController::class, "refreshToken"]);
//     Route::get("logout", [AuthController::class, "logout"]);

// });

// role based authentication
// Route::middleware('auth')->group(function () {
//     Route::middleware('role:admin')->get('/admin/dashboard', [AuthController::class, 'admin']);
//     Route::middleware('role:owner')->get('/owner/dashboard', [AuthController::class, 'owner']);
//     Route::middleware('role:staff')->get('/staff/dashboard', [AuthController::class, 'staff']);
// });

    Route::post('/login', [AuthController::class,'login']);
    Route::post('/register', [AuthController::class, 'register']);
    route::post('/logout',[AuthController::class,'logout']);

//customer

Route::get('/getCustomer',[CustomerController::class, 'getCustomer']);
Route::post('/addCustomer',[CustomerController::class,'addCustomer']);
Route::put('/updateCustomer/{id}',[CustomerController::class,'updateCustomer']);
Route::delete('/deleteCustomer/{id}',[CustomerController::class,'deleteCustomer']);
Route::get('/getCustomerById/{id}',[CustomerController::class, 'getCustomerById']);

//staff

Route::get('/getStaff',[StaffController::class,'getAllStaff']);
Route::post('/addStaff',[StaffController::class,'addStaff']);
Route::get('/getStaffById/{id}',[StaffController::class,'getStaffById']);

Route::delete('/deleteStaff/{id}',[StaffController::class,'deleteStaff']);
Route::put('/updateStaff/{id}',[StaffController::class,'updateStaff']);

Route::get('/getStaffByRole',[StaffController::class.'getStaffByRole']);

//service

Route::get('/getAllService', [ServiceController::class,'storeService']);
Route::post('/addService',[ServiceController::class,'addService']);
Route::put('/updateService/{id}',[ServiceController::class, 'updateService']);
Route::delete('/deleteService/{id}',[ServiceController::class,'deleteService']);
Route::get('/getServiceById/{id}',[ServiceController::class, 'getServiceById']);

//Appointment

Route::get('/getAllAppointment',[AppointmentController::class,'getAllAppointment']);
Route::post('/addAppointment',[AppointmentController::class,'addAppointment']);
Route::post('/addCustomerDetails',[AppointmentController::class, 'addCustomerDetails']);
Route::delete('/deleteAppointment/{id}',[AppointmentController::class,'deleteAppointment']);
Route::get('/getAllTimeSlots', [AppointmentController::class,'getAllTimeSlots']);
Route::get('/getUpcomingAppointment',[AppointmentController::class,'getUpcomingAppointment']);
Route::get('/getStaffAvailability',[AppointmentController::class,'getStaffAvailability']);
Route::post('/appointmentWithInvoice',[AppointmentController::class,'appointmentWithInvoice']);
Route::get('/getAppointmentById/{id}',[AppointmentController::class,'getAppointmentById']);
Route::put('/updateAppointment/{id}',[AppointmentController::class,'updateAppointment']);

//invoice

Route::get('/getAllInvoice',[InvoiceController::class,'getAllInvoice']);
Route::get('/getInvoiceById/{id}',[InvoiceController::class,'getInvoiceById']);