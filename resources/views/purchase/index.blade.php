@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Purchases</h3>

                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Inv #</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $key => $purchase)
                                @php
                                    $amount = $purchase->total;
                                    $paid = $purchase->payments->sum('amount');
                                    $due = $amount - $paid;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $purchase->inv ?? "-" }}</td>
                                    <td>{{ $purchase->vendor->title }}</td>
                                    <td>{{ date('d M Y', strtotime($purchase->date)) }}</td>
                                    <td>{{ number_format($amount) }}</td>
                                  {{--   <td>{{ number_format($paid) }}</td>
                                    <td>{{ number_format($due) }}</td> --}}
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('purchase.show', $purchase->id)}}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" onclick="newWindow('{{route('purchase.edit', $purchase->id)}}')">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Edit
                                                    </a>
                                                </li>
                                                {{-- <li>
                                                    <a class="dropdown-item" onclick="newWindow('{{route('purchasePayment.index', $purchase->id)}}')">
                                                        <i class="ri-money-dollar-circle-fill align-bottom me-2 text-muted"></i>
                                                        Payments
                                                    </a>
                                                </li> --}}
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{route('purchases.delete', $purchase->id)}}">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{$purchases->links()}}
                </div>
            </div>
        </div>
    </div>
    <!-- Default Modals -->
@endsection

