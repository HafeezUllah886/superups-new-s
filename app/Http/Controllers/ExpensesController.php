<?php
 namespace App\Http\Controllers; use App\Models\accounts; use App\Models\expenses; use App\Models\transactions; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; class ExpensesController extends Controller { public function index() { $expenses = expenses::orderby("\x69\144", "\144\x65\163\x63")->get(); $accounts = accounts::business()->get(); return view("\106\151\x6e\141\156\x63\x65\56\145\x78\160\145\156\x73\x65\56\151\156\144\145\x78", compact("\145\170\160\145\x6e\163\145\163", "\x61\x63\x63\x6f\165\156\164\x73")); } public function create() { } public function store(Request $request) { try { DB::beginTransaction(); $ref = getRef(); expenses::create(array("\141\143\x63\157\165\x6e\164\111\x44" => $request->accountID, "\x61\155\157\165\156\164" => $request->amount, "\x64\x61\x74\145" => $request->date, "\x6e\x6f\164\145\x73" => $request->notes, "\x72\145\146\x49\104" => $ref)); createTransaction($request->accountID, $request->date, 0, $request->amount, "\x45\170\160\x65\156\163\145\x20\55\x20" . $request->notes, $ref); DB::commit(); return back()->with("\x73\x75\x63\143\145\x73\x73", "\105\170\160\x65\x6e\163\145\40\x53\x61\166\145\x64"); } catch (\Exception $e) { DB::rollBack(); return back()->with("\145\x72\162\157\x72", $e->getMessage()); } } public function show(expenses $expenses) { } public function edit(expenses $expenses) { } public function update(Request $request, expenses $expenses) { } public function delete($ref) { try { DB::beginTransaction(); expenses::where("\162\x65\146\x49\104", $ref)->delete(); transactions::where("\162\145\146\111\x44", $ref)->delete(); DB::commit(); session()->forget("\x63\157\156\x66\151\x72\155\145\x64\x5f\160\x61\163\x73\167\157\162\x64"); return redirect()->route("\145\x78\160\145\156\x73\145\x73\56\x69\x6e\x64\145\170")->with("\163\165\143\x63\x65\163\163", "\x45\x78\160\145\x6e\x73\x65\40\x44\x65\154\x65\x74\145\x64"); } catch (\Exception $e) { DB::rollBack(); session()->forget("\143\157\156\146\151\162\155\x65\144\x5f\160\141\x73\x73\167\157\x72\x64"); return redirect()->route("\x65\170\160\x65\156\163\x65\x73\x2e\x69\x6e\x64\x65\170")->with("\x65\x72\162\x6f\x72", $e->getMessage()); } } }
