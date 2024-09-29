<?php
 namespace App\Http\Controllers; use App\Models\accounts; use App\Models\categories; use App\Models\products; use App\Models\sale_details; use App\Models\sale_payments; use App\Models\sales; use App\Models\salesman; use App\Models\stock; use App\Models\transactions; use App\Models\units; use App\Models\warehouses; use Pdf; use Exception; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Spatie\Browsershot\Browsershot; class SalesController extends Controller { public function index() { $sales = sales::with("\160\x61\x79\x6d\x65\x6e\164\x73")->orderby("\x69\x64", "\144\145\x73\143")->paginate(10); return view("\163\x61\154\145\163\56\x69\156\144\145\170", compact("\163\141\154\145\163")); } public function create() { $products = products::orderby("\156\x61\x6d\x65", "\141\163\143")->get(); $warehouses = warehouses::all(); $customers = accounts::customer()->get(); $accounts = accounts::business()->get(); $cats = categories::orderBy("\156\x61\155\x65", "\141\x73\x63")->get(); return view("\x73\141\x6c\x65\163\56\143\162\145\141\164\145", compact("\160\x72\x6f\x64\165\143\164\x73", "\x77\141\x72\x65\150\157\x75\163\145\x73", "\143\165\163\164\157\155\145\x72\x73", "\141\x63\143\157\x75\156\x74\x73", "\x63\141\x74\x73")); } public function store(Request $request) { try { if ($request->isNotFilled("\x69\x64")) { throw new Exception("\x50\x6c\145\x61\163\x65\40\x53\x65\x6c\x65\143\x74\x20\x41\164\154\145\141\x73\164\40\x4f\156\145\40\120\x72\157\x64\165\143\164"); } DB::beginTransaction(); $ref = getRef(); $sale = sales::create(array("\143\x75\163\164\157\x6d\145\162\x49\104" => $request->customerID, "\x64\141\x74\145" => $request->date, "\x6e\157\164\x65\163" => $request->notes, "\x64\x69\163\x63\x6f\165\156\x74" => $request->discount, "\x64\143" => $request->dc, "\x72\x65\146\x49\x44" => $ref)); $ids = $request->id; $total = 0; foreach ($ids as $key => $id) { if ($request->amount[$key] > 0) { $qty = $request->qty[$key]; $price = $request->price[$key]; $total += $request->amount[$key]; sale_details::create(array("\163\141\x6c\145\163\x49\x44" => $sale->id, "\160\162\x6f\144\x75\x63\164\111\x44" => $id, "\x70\162\x69\143\x65" => $price, "\x77\141\x72\x65\x68\157\x75\163\x65\111\x44" => $request->warehouse[$key], "\161\x74\x79" => $qty, "\x61\155\157\165\x6e\164" => $request->amount[$key], "\144\x61\x74\145" => $request->date, "\162\145\x66\x49\104" => $ref)); createStock($id, 0, $qty, $request->date, "\x53\157\x6c\144\x20\x69\x6e\x20\111\x6e\x76\40\x23\40{$sale->id}", $ref, $request->warehouse[$key]); } } $discount = $request->discount; $dc = $request->dc; $net = $total + $dc - $discount; $sale->update(array("\164\x6f\164\x61\154" => $net)); if ($request->status == "\x70\141\x69\x64") { sale_payments::create(array("\163\141\x6c\145\x73\111\104" => $sale->id, "\141\143\x63\157\x75\x6e\x74\x49\x44" => $request->accountID, "\x64\141\164\x65" => $request->date, "\141\x6d\157\x75\156\x74" => $net, "\156\x6f\164\x65\x73" => "\106\165\x6c\x6c\40\x50\141\151\144", "\x72\x65\146\x49\104" => $ref)); createTransaction($request->accountID, $request->date, $net, 0, "\120\x61\x79\155\x65\x6e\x74\40\x6f\x66\40\x49\x6e\166\40\x4e\x6f\x2e\x20{$sale->id}", $ref); } elseif ($request->status == "\x61\x64\x76\141\156\x63\145\144") { $balance = getAccountBalance($request->customerID); if ($net < $balance) { createTransaction($request->customerID, $request->date, $net, 0, "\120\145\156\x64\x69\156\147\40\101\155\157\x75\156\164\x20\x6f\x66\x20\111\156\166\x20\116\157\56\x20{$sale->id}", $ref); DB::commit(); return back()->with("\x73\165\143\143\x65\163\163", "\123\141\154\x65\40\x43\162\145\141\164\145\x64\72\x20\x42\x61\x6c\141\x6e\x63\145\x20\167\141\x73\40\156\x6f\x74\x20\145\156\x6f\165\147\150\x20\155\157\x76\x65\144\40\164\157\40\x75\156\160\x61\151\x64\x20\x2f\40\x70\145\x6e\x64\151\x6e\147"); } sale_payments::create(array("\163\141\154\145\163\x49\x44" => $sale->id, "\x61\x63\x63\x6f\x75\156\x74\x49\x44" => $request->accountID, "\144\x61\x74\x65" => $request->date, "\x61\155\x6f\165\x6e\x74" => $net, "\156\x6f\x74\145\x73" => "\x46\x75\x6c\154\x20\120\141\151\x64", "\162\x65\146\x49\x44" => $ref)); createTransaction($request->customerID, $request->date, $net, 0, "\x49\156\x76\40\116\157\x2e\x20{$sale->id}", $ref); } else { createTransaction($request->customerID, $request->date, $net, 0, "\x50\145\156\x64\151\x6e\x67\x20\x41\x6d\157\x75\156\164\x20\157\146\x20\111\x6e\166\x20\116\157\56\x20{$sale->id}", $ref); } DB::commit(); return to_route("\163\x61\x6c\x65\x2e\163\x68\157\167", $sale->id)->with("\x73\x75\143\143\x65\x73\163", "\x53\141\x6c\x65\x20\103\162\x65\141\x74\x65\144"); } catch (\Exception $e) { DB::rollback(); return back()->with("\145\x72\162\x6f\162", $e->getMessage()); } } public function show(sales $sale) { return view("\163\141\154\x65\163\56\166\x69\145\x77", compact("\x73\141\x6c\145")); } public function pdf($id) { $sale = sales::find($id); $pdf = Pdf::loadview("\163\x61\154\x65\x73\x2e\x70\144\x66", compact("\x73\141\154\145")); return $pdf->download("\111\x6e\166\157\151\x63\x65\x20\116\157\56\40{$sale->id}\x2e\160\x64\146"); } public function edit(sales $sale) { $products = products::orderby("\x6e\141\x6d\x65", "\x61\x73\x63")->get(); $warehouses = warehouses::all(); $customers = accounts::customer()->get(); $accounts = accounts::business()->get(); return view("\163\x61\x6c\x65\x73\56\x65\x64\x69\x74", compact("\x70\162\x6f\x64\165\143\164\x73", "\167\x61\162\x65\x68\x6f\165\x73\145\163", "\x63\x75\x73\x74\x6f\155\x65\x72\163", "\x61\143\143\x6f\x75\x6e\164\163", "\x73\141\154\145")); } public function update(Request $request, $id) { try { DB::beginTransaction(); $sale = sales::find($id); foreach ($sale->payments as $payment) { transactions::where("\x72\x65\146\x49\104", $payment->refID)->delete(); $payment->delete(); } foreach ($sale->details as $product) { stock::where("\x72\145\146\111\x44", $product->refID)->delete(); $product->delete(); } transactions::where("\162\x65\x66\x49\x44", $sale->refID)->delete(); $ref = $sale->refID; $sale->update(array("\x63\165\x73\x74\157\x6d\x65\162\x49\x44" => $request->customerID, "\x64\141\x74\145" => $request->date, "\156\157\164\145\x73" => $request->notes, "\144\x69\163\143\157\x75\156\x74" => $request->discount, "\x64\143" => $request->dc)); $ids = $request->id; $total = 0; foreach ($ids as $key => $id) { if ($request->amount[$key] > 0) { $qty = $request->qty[$key]; $price = $request->price[$key]; $total += $request->amount[$key]; sale_details::create(array("\x73\141\154\x65\163\111\x44" => $sale->id, "\x70\162\157\144\165\x63\x74\x49\104" => $id, "\x70\162\151\143\x65" => $price, "\167\x61\162\145\150\x6f\x75\163\x65\x49\104" => $request->warehouse[$key], "\161\164\x79" => $qty, "\141\x6d\x6f\x75\156\x74" => $request->amount[$key], "\x64\x61\164\145" => $request->date, "\x72\145\146\111\x44" => $ref)); createStock($id, 0, $qty, $request->date, "\x53\157\154\144\x20\x69\x6e\x20\x49\x6e\x76\x20\x23\x20{$sale->id}", $ref, $request->warehouse[$key]); } } $discount = $request->discount; $dc = $request->dc; $net = $total + $dc - $discount; $sale->update(array("\164\157\x74\x61\x6c" => $net)); if ($request->status == "\x70\141\x69\x64") { sale_payments::create(array("\x73\141\x6c\145\163\x49\104" => $sale->id, "\x61\143\x63\x6f\x75\x6e\x74\x49\x44" => $request->accountID, "\144\141\164\x65" => $request->date, "\x61\x6d\x6f\165\156\164" => $net, "\x6e\157\x74\145\x73" => "\106\165\154\154\x20\120\x61\x69\144", "\162\145\x66\111\x44" => $ref)); createTransaction($request->accountID, $request->date, $net, 0, "\x50\x61\x79\x6d\x65\156\x74\x20\x6f\x66\40\x49\x6e\166\40\x4e\x6f\56\x20{$sale->id}", $ref); } elseif ($request->status == "\x61\144\x76\x61\156\143\145\x64") { $balance = getAccountBalance($request->customerID); if ($net < $balance) { createTransaction($request->customerID, $request->date, $net, 0, "\x50\145\x6e\144\151\x6e\x67\40\101\x6d\157\x75\x6e\x74\40\157\x66\x20\111\x6e\x76\40\116\157\x2e\x20{$sale->id}", $ref); DB::commit(); return back()->with("\x73\165\x63\143\x65\x73\x73", "\123\141\154\x65\x20\103\x72\145\x61\x74\x65\144\72\40\102\x61\154\x61\156\x63\145\40\x77\141\163\x20\156\x6f\x74\x20\145\x6e\157\165\147\150\x20\155\x6f\166\x65\144\x20\164\x6f\x20\165\156\160\141\x69\x64\x20\57\x20\x70\145\156\144\151\156\147"); } sale_payments::create(array("\x73\141\x6c\145\x73\x49\x44" => $sale->id, "\141\143\143\157\165\156\x74\x49\104" => $request->accountID, "\x64\x61\x74\145" => $request->date, "\141\x6d\157\x75\156\164" => $net, "\156\157\x74\145\x73" => "\106\165\154\154\40\x50\141\151\x64", "\162\145\x66\111\104" => $ref)); createTransaction($request->customerID, $request->date, $net, 0, "\111\156\x76\40\x4e\157\x2e\x20{$sale->id}", $ref); } else { createTransaction($request->customerID, $request->date, $net, 0, "\120\x65\x6e\x64\151\156\x67\x20\x41\x6d\x6f\165\156\x74\x20\x6f\x66\40\x49\x6e\166\x20\116\x6f\56\x20{$sale->id}", $ref); } DB::commit(); return to_route("\x73\x61\x6c\x65\56\151\x6e\144\x65\x78")->with("\163\165\x63\x63\145\x73\x73", "\123\141\154\x65\x20\125\160\144\141\x74\145\144"); } catch (\Exception $e) { DB::rollBack(); return to_route("\x73\x61\154\x65\x2e\151\156\x64\145\170")->with("\x65\x72\x72\157\162", $e->getMessage()); } } public function destroy($id) { try { DB::beginTransaction(); $sale = sales::find($id); foreach ($sale->payments as $payment) { transactions::where("\x72\x65\x66\111\104", $payment->refID)->delete(); $payment->delete(); } foreach ($sale->details as $product) { stock::where("\162\x65\x66\x49\104", $product->refID)->delete(); $product->delete(); } transactions::where("\162\145\x66\111\104", $sale->refID)->delete(); $sale->delete(); DB::commit(); session()->forget("\143\x6f\156\146\x69\x72\x6d\145\x64\x5f\x70\x61\x73\x73\x77\x6f\162\144"); return to_route("\163\141\154\x65\56\151\x6e\144\x65\170")->with("\163\x75\x63\143\x65\163\163", "\123\x61\154\x65\40\x44\x65\x6c\x65\x74\x65\144"); } catch (\Exception $e) { DB::rollBack(); session()->forget("\143\x6f\x6e\x66\x69\162\x6d\x65\144\137\160\x61\163\163\x77\157\162\x64"); return to_route("\x73\x61\154\145\x2e\x69\156\144\145\170")->with("\x65\x72\162\157\x72", $e->getMessage()); } } public function getSignleProduct($id) { $product = products::with("\165\156\151\164")->find($id); return $product; } }
