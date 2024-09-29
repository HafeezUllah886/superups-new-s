<?php
 use App\Models\products; use App\Models\purchase_details; use App\Models\ref; use App\Models\sale_details; use App\Models\stock; use Carbon\Carbon; goto nfXZv; X5r1Y: function firstDayOfMonth() { $startOfMonth = Carbon::now()->startOfMonth(); return $startOfMonth->format("\x59\x2d\x6d\55\x64"); } goto HAtC2; nfXZv: function getRef() { $ref = ref::first(); if ($ref) { $ref->ref = $ref->ref + 1; } else { $ref = new ref(); $ref->ref = 1; } $ref->save(); return $ref->ref; } goto X5r1Y; iKOhh: function avgPurchasePrice($from, $to, $id) { $purchases = purchase_details::where("\x70\162\x6f\x64\165\143\164\111\x44", $id); if ($from != "\141\154\154" && $to != "\x61\154\154") { $purchases->whereBetween("\144\141\x74\145", array($from, $to)); } $purchase_amount = $purchases->sum("\141\155\157\x75\156\x74"); $purchase_qty = $purchases->sum("\161\164\x79"); if ($purchase_qty > 0) { $purchase_price = $purchase_amount / $purchase_qty; } else { $purchase_price = 0; } return $purchase_price; } goto tWkf8; qQSeG: function createStock($id, $cr, $db, $date, $notes, $ref, $warehouse) { stock::create(array("\160\x72\157\x64\165\143\x74\111\x44" => $id, "\x63\x72" => $cr, "\144\142" => $db, "\x64\141\x74\x65" => $date, "\156\x6f\x74\145\x73" => $notes, "\x72\145\146\111\104" => $ref, "\167\141\x72\145\x68\x6f\x75\163\x65\111\104" => $warehouse)); } goto R1HjY; tWkf8: function stockValue() { $products = products::all(); $value = 0; foreach ($products as $product) { $value += productStockValue($product->id); } return $value; } goto iWip7; Amk1_: function avgSalePrice($from, $to, $id) { $sales = sale_details::where("\160\x72\157\144\165\143\x74\x49\x44", $id); if ($from != "\x61\154\154" && $to != "\141\154\x6c") { $sales->whereBetween("\x64\141\164\145", array($from, $to)); } $sales_amount = $sales->sum("\141\155\x6f\x75\x6e\x74"); $sales_qty = $sales->sum("\161\x74\x79"); if ($sales_qty > 0) { $sale_price = $sales_amount / $sales_qty; } else { $sale_price = 0; } return $sale_price; } goto iKOhh; R1HjY: function getStock($id) { $stocks = stock::where("\x70\162\157\x64\165\x63\x74\111\x44", $id)->get(); $balance = 0; foreach ($stocks as $stock) { $balance += $stock->cr; $balance -= $stock->db; } return $balance; } goto Amk1_; HAtC2: function lastDayOfMonth() { $endOfMonth = Carbon::now()->endOfMonth(); return $endOfMonth->format("\x59\55\x6d\x2d\x64"); } goto qQSeG; iWip7: function productStockValue($id) { $stock = getStock($id); $price = avgPurchasePrice("\141\154\x6c", "\x61\x6c\x6c", $id); return $price * $stock; }
