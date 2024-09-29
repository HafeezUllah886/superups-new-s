<?php
 namespace App\Http\Controllers; use App\Models\accounts; use App\Models\transactions; use Barryvdh\DomPDF\Facade\Pdf; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Symfony\Component\Finder\Exception\AccessDeniedException; class AccountsController extends Controller { public function index($filter) { $accounts = accounts::where("\164\x79\160\x65", $filter)->orderBy("\x74\x69\164\154\145", "\141\x73\143")->get(); return view("\106\151\x6e\141\156\x63\x65\56\x61\143\143\157\x75\156\164\x73\56\x69\156\144\x65\x78", compact("\x61\x63\143\x6f\x75\156\164\x73", "\146\151\154\164\145\162")); } public function create() { return view("\106\x69\156\x61\156\143\x65\56\x61\x63\143\157\x75\156\x74\x73\56\143\162\145\141\x74\x65"); } public function store(Request $request) { $request->validate(array("\x74\x69\164\x6c\145" => "\162\145\161\x75\151\x72\x65\144\174\165\x6e\x69\x71\x75\x65\72\141\143\x63\157\x75\x6e\x74\163\x2c\164\x69\x74\x6c\145"), array("\x74\x69\164\x6c\145\x2e\162\145\x71\165\x69\162\x65\x64" => "\120\x6c\145\x61\x73\x65\40\105\156\164\145\162\x20\101\x63\143\x6f\x75\156\164\40\x54\151\x74\154\x65", "\x74\151\164\x6c\x65\56\165\x6e\x69\161\x75\145" => "\x41\143\143\x6f\x75\156\164\40\167\x69\x74\x68\x20\164\x68\151\163\40\x74\x69\x74\154\145\40\x61\x6c\162\145\141\x64\x79\40\x65\x78\151\163\164\x73")); try { DB::beginTransaction(); $ref = getRef(); if ($request->type == "\x43\165\163\164\157\x6d\145\x72") { $account = accounts::create(array("\164\x69\164\154\145" => $request->title, "\x74\x79\160\145" => $request->type, "\x63\x61\x74\145\147\157\x72\171" => $request->category, "\143\x6f\x6e\164\x61\143\x74" => $request->contact, "\x61\144\x64\162\145\163\x73" => $request->address)); } else { $account = accounts::create(array("\x74\151\164\x6c\145" => $request->title, "\164\x79\160\145" => $request->type, "\x63\141\164\x65\x67\157\162\x79" => $request->category)); } if ($request->initial > 0) { if ($request->initialType == "\x30") { createTransaction($account->id, now(), $request->initial, 0, "\111\156\x69\x74\151\x61\x6c\40\101\155\157\x75\x6e\164", $ref); } else { createTransaction($account->id, now(), 0, $request->initial, "\111\x6e\x69\164\x69\141\154\x20\x41\155\x6f\165\x6e\x74", $ref); } } DB::commit(); return back()->with("\x73\x75\143\143\x65\163\x73", "\101\143\x63\157\x75\x6e\x74\40\103\x72\145\x61\164\145\144\x20\123\165\143\143\x65\x73\x73\x66\x75\x6c\x6c\171"); } catch (\Exception $e) { return back()->with("\x65\162\x72\x6f\162", $e->getMessage()); } } public function show($id, $from, $to) { $account = accounts::find($id); $transactions = transactions::where("\x61\143\143\157\165\156\164\x49\x44", $id)->whereBetween("\x64\x61\x74\145", array($from, $to))->get(); $pre_cr = transactions::where("\141\143\x63\157\165\156\164\x49\x44", $id)->whereDate("\144\x61\x74\145", "\x3c", $from)->sum("\143\x72"); $pre_db = transactions::where("\x61\143\x63\x6f\x75\156\164\111\104", $id)->whereDate("\x64\x61\164\x65", "\74", $from)->sum("\x64\x62"); $pre_balance = $pre_cr - $pre_db; $cur_cr = transactions::where("\x61\143\x63\x6f\165\x6e\x74\x49\x44", $id)->sum("\x63\x72"); $cur_db = transactions::where("\141\x63\143\x6f\x75\x6e\x74\x49\104", $id)->sum("\144\142"); $cur_balance = $cur_cr - $cur_db; return view("\106\x69\156\141\156\x63\x65\56\x61\x63\x63\157\165\156\x74\163\56\x73\x74\x61\164\x6d\145\x6e\x74", compact("\x61\143\143\x6f\165\156\164", "\164\x72\141\x6e\x73\141\143\x74\151\x6f\156\163", "\160\162\x65\137\142\141\154\141\156\143\x65", "\x63\165\x72\x5f\142\141\x6c\x61\x6e\x63\x65", "\x66\162\157\155", "\x74\157")); } public function pdf($id, $from, $to) { $account = accounts::find($id); $transactions = transactions::where("\x61\x63\143\x6f\165\156\x74\x49\x44", $id)->whereBetween("\144\141\164\145", array($from, $to))->get(); $pre_cr = transactions::where("\141\x63\143\157\x75\x6e\164\111\x44", $id)->whereDate("\144\x61\x74\x65", "\74", $from)->sum("\143\162"); $pre_db = transactions::where("\x61\143\143\157\x75\156\x74\x49\x44", $id)->whereDate("\x64\141\x74\145", "\x3c", $from)->sum("\144\142"); $pre_balance = $pre_cr - $pre_db; $cur_cr = transactions::where("\141\143\x63\157\165\156\164\111\x44", $id)->sum("\x63\162"); $cur_db = transactions::where("\141\x63\143\157\165\156\164\111\104", $id)->sum("\x64\x62"); $cur_balance = $cur_cr - $cur_db; $pdf = Pdf::loadview("\106\151\156\141\156\x63\x65\56\x61\x63\143\157\165\x6e\x74\x73\56\x70\144\146", compact("\x61\143\143\157\165\x6e\164", "\164\x72\141\x6e\163\141\143\164\151\x6f\156\x73", "\x70\162\x65\137\x62\141\x6c\141\x6e\x63\145", "\x63\x75\x72\137\x62\141\x6c\x61\x6e\143\145", "\x66\162\x6f\x6d", "\164\x6f")); return $pdf->download("\x41\x63\143\157\165\156\x74\x20\x53\164\141\x74\145\x6d\145\156\164\40\55\40{$account->id}\56\160\144\x66"); } public function edit(accounts $account) { return view("\106\151\x6e\x61\x6e\x63\x65\56\141\143\143\x6f\x75\x6e\164\x73\x2e\x65\144\151\x74", compact("\x61\x63\143\157\x75\x6e\164")); } public function update(Request $request, accounts $account) { $request->validate(array("\164\151\164\154\145" => "\162\x65\161\x75\x69\x72\145\x64\x7c\x75\156\151\161\x75\x65\x3a\x61\143\143\157\165\156\x74\x73\x2c\x74\x69\x74\154\x65\x2c" . $request->accountID), array("\x74\151\x74\x6c\x65\x2e\x72\x65\x71\x75\151\x72\x65\144" => "\x50\154\x65\x61\163\x65\x20\x45\156\164\x65\162\40\101\143\143\x6f\x75\x6e\164\40\x54\x69\x74\154\145", "\x74\x69\164\154\x65\x2e\x75\156\x69\161\165\x65" => "\101\143\143\x6f\165\x6e\x74\40\x77\151\164\x68\x20\x74\x68\x69\163\40\164\x69\x74\154\x65\40\141\154\x72\x65\x61\144\171\40\145\170\x69\163\x74\163")); $account = accounts::find($request->accountID)->update(array("\164\x69\164\154\x65" => $request->title, "\x63\x61\164\145\x67\x6f\162\x79" => $request->category, "\x63\157\x6e\164\141\x63\164" => $request->contact ?? null, "\x61\144\x64\x72\x65\x73\x73" => $request->address ?? null)); return redirect()->route("\141\143\x63\157\165\156\x74\x73\114\151\163\164", $request->type)->with("\163\165\x63\x63\145\x73\163", "\x41\x63\143\x6f\165\x6e\x74\x20\125\160\x64\x61\164\145\144"); } public function destroy(accounts $accounts) { } }
