<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceCoverTitleRequest;
use App\Models\DefaultTableAttribute;
use App\Models\InvoiceCoverTitle;
use App\Models\InvoiceCoverTitleItem;
use App\Models\TableAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvoiceCoverTitleController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $invoice_cover_titles = InvoiceCoverTitle::query()->with(["user","items" => function($query){
                $query->where("invoice_cover_titles_items.is_operable","=",1);
            }])->where("invoice_cover_titles.is_operable","=",1)->get();
            return view("admin.invoice_cover_titles",["invoice_cover_titles" => $invoice_cover_titles]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(InvoiceCoverTitleRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $invoice_cover_title = InvoiceCoverTitle::query()->create($validated);
            $default_attributes = DefaultTableAttribute::attributes("invoice_cover");
            $insertion_array = [];
            foreach ($default_attributes as $attribute)
                $insertion_array[] = new InvoiceCoverTitleItem(["name" => $attribute->name,"kind" => $attribute->kind,"is_operable" => 0]);
            $invoice_cover_title->items()->saveMany($insertion_array);
            $custom_titles = json_decode($validated["invoice_cover_list"],true);
            foreach ($custom_titles as $title){
                $invoice_cover_title->items()->create([
                    "name" => $title["name"],
                    "kind" => $title["kind"]
                ]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $invoice_cover_title = InvoiceCoverTitle::query()->with(["user","items" => function($query){
                $query->where("invoice_cover_titles_items.is_operable","=",1);
            }])->findOrFail($id);
            return view("admin.edit_invoice_cover_title",["invoice_cover_title" => $invoice_cover_title,"items" => InvoiceCoverTitle::make_items_list($id)]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(InvoiceCoverTitleRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $invoice_cover_title = InvoiceCoverTitle::query()->findOrFail($id);
            $invoice_cover_title->items()->where("is_operable","=",1)->delete();
            $custom_titles = json_decode($validated["invoice_cover_list"],true);
            foreach ($custom_titles as $title){
                $invoice_cover_title->items()->create([
                    "name" => $title["name"],
                    "kind" => $title["kind"]
                ]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice_cover_title = InvoiceCoverTitle::query()->with("invoices")->findOrFail($id);
            if ($invoice_cover_title->invoices()->exists())
                return redirect()->back()->withErrors(["logical" => "به دلیل وجود رابطه(های) با اتوماسیون وضعیت، امکان حذف رکورد وجود ندارد"]);
            $invoice_cover_title->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
