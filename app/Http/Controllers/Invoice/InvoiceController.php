<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Medicine;
use App\Models\Service;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\InvoiceDetailRequest;
use App\Http\Requests\InvoiceDetailUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Models\Province;
use App\Models\District;
use Auth;

class InvoiceController extends Controller
{
	
	protected $invoice;

	public function __construct(InvoiceRepository $repository)
	{
		$this->invoice = $repository;
	}

	public function getDatatable(Request $request)
	{
		return $this->invoice->getDatatable($request);
	}

	public function index()
	{
		return view('invoice.index');
	}


	public function create()
	{
		$this->data = [
			'provinces' => Province::getSelectData(),
			'districts' => [],
			'inv_number' => $this->invoice->inv_number(),
			'medicines' => Medicine::getSelectData('id', 'name', '', 'name' ,'asc'),
			'services' => Service::select('id', 'name', 'price', 'description')->orderBy('name' ,'asc')->get(),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
		];
		return view('invoice.create', $this->data);
	}


	public function store(InvoiceRequest $request)
	{
		
		$invoice = $this->invoice->create($request);
		if ($invoice) {
			// Redirect
			return redirect()->route('invoice.edit', $invoice->id)
				->with('success', __('alert.crud.success.create', ['name' => Auth::user()->module()]) . $request->date);
		}
	}

	public function save_order(Request $request, Invoice $invoice)
	{
		if ($this->invoice->save_order($request)) {
			// Redirect
			return redirect()->route('invoice.edit', $invoice->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]) . str_pad($invoice->inv_number, 6, "0", STR_PAD_LEFT));
		}
	}

	public function show(Invoice $invoice)
	{
		//
	}

	
	public function getItemDetail(Request $request)
	{
		return response()->json([ 'invoice_detail' => InvoiceDetail::find($request->id) ]);
	}

	public function get_edit_detail(Request $request)
	{
		return $this->invoice->get_edit_detail($request->id);
	}

	public function deleteInvoiceDetail(Request $request)
	{
		return $this->invoice->deleteInvoiceDetail($request);
	}
	
	public function invoiceDetailStore(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'price' => 'required|numeric',
			// 'discount' => 'required',
			// 'description' => 'required',
		]);
		
		if ($validator->fails())
		{
				return response()->json(['errors'=>$validator->errors()]);
		}
		return $this->invoice->invoiceDetailStore($request);
	}

	public function invoiceDetailUpdate(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'price' => 'required|numeric',
			// 'discount' => 'required',
			// 'description' => 'required',
		]);
		
		if ($validator->fails())
		{
				return response()->json(['errors'=>$validator->errors()]);
		}
		return $this->invoice->invoiceDetailUpdate($request);
	}

	public function getInvoicePreview(Request $request)
	{
		return $this->invoice->getInvoicePreview($request->id);
	}

	public function invoice_detail(Invoice $invoice)
	{
		$this->data = [
			'invoice' => $invoice,
			'services' => Service::getSelectService($invoice->service_id),
			'invoice_preview' => $this->invoice->getInvoicePreview($invoice->id),
		];

		return view('invoice.invoice_detail', $this->data);
	}


	public function edit(Invoice $invoice)
	{
		$this->data = [
			'invoice' => $invoice,
			'provinces' => Province::getSelectData(),
			'districts' => $invoice->pt_province_id && $invoice->pt_district_id ? $invoice->province->getSelectDistrict() : [],
			'medicines' => Medicine::getSelectData('id', 'name', '', 'name' ,'asc'),
			'services' => Service::select('id', 'name', 'price', 'description')->orderBy('name' ,'asc')->get(),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
			'invoice_preview' => $this->invoice->getInvoicePreview($invoice->id)->getData()->invoice_detail,
		];

		return view('invoice.edit', $this->data);
	}

	public function print(Invoice $invoice)
	{

		$this->data = [
			'invoice' => $invoice,
			'invoice_preview' => $this->invoice->getInvoicePreview($invoice->id)->getData()->invoice_detail,
		];

		return view('invoice.print', $this->data);
	}

	public function update(InvoiceRequest $request, Invoice $invoice)
	{
		if ($this->invoice->update($request, $invoice)) {

			// Redirect
			return redirect()->route('invoice.edit', $invoice->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]) . str_pad($request->inv_number, 6, "0", STR_PAD_LEFT));
		}
	}

	public function status(Request $request)
	{
		// $invoice = Invoice::find($request->id);
		return $this->invoice->status($request);
	}


	public function destroy(Request $request, Invoice $invoice)
	{
		// Redirect
		return redirect()->route('invoice.index')
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . str_pad(($this->invoice->destroy($request, $invoice)), 6, "0", STR_PAD_LEFT));
	}

	public function invoice_detail_destroy(InvoiceDetail $invoice_detail)
	{
		// Redirect
		return redirect()->route('invoice.edit', $invoice_detail->invoice_id)
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . $this->invoice->destroy_invoice_detail($invoice_detail));
	}
}
