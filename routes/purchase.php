<?php
 use App\Http\Controllers\PurchaseController; use App\Http\Controllers\PurchasePaymentsController; use App\Http\Middleware\confirmPassword; use Illuminate\Support\Facades\Route; Route::middleware("\x61\x75\164\150")->group(function () { Route::resource("\160\165\162\143\x68\x61\x73\145", PurchaseController::class); Route::get("\160\x75\162\x63\150\141\163\145\163\57\x67\145\164\160\162\x6f\144\165\x63\164\57\173\x69\x64\175", array(PurchaseController::class, "\x67\145\x74\x53\x69\147\x6e\x6c\x65\x50\162\x6f\x64\165\143\x74")); Route::get("\x70\x75\x72\x63\150\x61\x73\145\x73\x2f\x64\145\154\x65\164\x65\x2f\173\151\144\x7d", array(PurchaseController::class, "\144\145\x73\x74\x72\157\171"))->name("\160\x75\x72\x63\x68\x61\x73\145\163\x2e\x64\145\x6c\145\164\145")->middleware(confirmPassword::class); Route::get("\x70\165\x72\143\x68\141\x73\x65\x70\141\171\x6d\x65\156\164\57\173\151\x64\x7d", array(PurchasePaymentsController::class, "\151\x6e\144\x65\170"))->name("\x70\x75\x72\x63\x68\141\163\145\120\141\x79\155\x65\x6e\164\56\151\x6e\144\145\170"); Route::get("\x70\165\x72\x63\x68\141\x73\145\160\x61\x79\155\145\x6e\164\57\x64\x65\154\x65\x74\x65\57\173\x69\x64\x7d\x2f\x7b\162\145\x66\x7d", array(PurchasePaymentsController::class, "\x64\x65\x73\x74\x72\x6f\x79"))->name("\160\165\162\143\150\141\x73\145\x50\x61\x79\155\x65\156\164\x2e\x64\x65\154\145\164\x65")->middleware(confirmPassword::class); Route::resource("\x70\165\x72\143\x68\141\163\x65\137\x70\141\171\x6d\145\x6e\x74", PurchasePaymentsController::class); });
