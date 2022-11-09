<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableAttributesRequest;
use App\Models\DefaultTableAttribute;
use App\Models\InvoiceCoverTitleItem;
use App\Models\TableAttribute;
use App\Models\TableAttributeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class TableAttributeController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $table_attributes = TableAttribute::query()->with(["user","items" => function($query){
                $query->where("table_attribute_items.is_operable","=",1);
            }])->where("table_attributes.is_operable","=",1)->get();
            return view("admin.table_attributes",["table_attributes" => $table_attributes]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(TableAttributesRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $table_attribute = TableAttribute::query()->create($validated);
            $default_attributes = DefaultTableAttribute::attributes($validated["type"]);
            $insertion_array = [];
            foreach ($default_attributes as $attribute)
                $insertion_array[] = new TableAttributeItem(["name" =>$attribute->name,"category" => $attribute->category != "ندارد" ? $attribute->category : "note" ,"kind" => $attribute->kind,"is_operable" => 0]);
            $table_attribute->items()->saveMany($insertion_array);
            $custom_attributes = json_decode($validated["attributes_list"],true);
            foreach ($custom_attributes as $attribute){
                $table_attribute->items()->create([
                    "name" => $attribute["name"],
                    "kind" => $attribute["kind"],
                    "category" => isset($attribute["category"]) && $validated["type"] == "invoice" ? $attribute["category"] : null,
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
            $table_attribute = TableAttribute::query()->with(["user","items" => function($query){
                $query->where("table_attribute_items.is_operable","=",1);
            }])->findOrFail($id);
            return view("admin.edit_table_attribute",["table_attribute" => $table_attribute,"items" => TableAttribute::make_items_list($id)]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(TableAttributesRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $table_attribute = TableAttribute::query()->findOrFail($id);
            $table_attribute->items()->delete();
            $default_attributes = DefaultTableAttribute::attributes($validated["type"]);
            $insertion_array = [];
            foreach ($default_attributes as $attribute)
                $insertion_array[] = new TableAttributeItem(["name" =>$attribute->name,"category" => $attribute->category != "ندارد" ? $attribute->category : "note" ,"kind" => $attribute->kind,"is_operable" => 0]);
            $table_attribute->items()->saveMany($insertion_array);
            $custom_attributes = json_decode($validated["attributes_list"],true);
            foreach ($custom_attributes as $attribute){
                $table_attribute->items()->create([
                    "name" => $attribute["name"],
                    "kind" => $attribute["kind"],
                    "category" => isset($attribute["category"]) && $validated["type"] == "invoice" ? $attribute["category"] : null,
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
            $table_attribute = TableAttribute::query()->with(["invoices","performances"])->findOrFail($id);
            if ($table_attribute->invoices()->exists() || $table_attribute->performances()->exists())
                return redirect()->back()->withErrors(["logical" => "به دلیل وجود رابطه(های) با اتوماسیون کارکرد و یا اتوماسیون وضعیت، امکان حذف رکورد مورد نظر وجود ندارد"]);
            $table_attribute->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
