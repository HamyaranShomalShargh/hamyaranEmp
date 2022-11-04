<?php

namespace App\Http\Controllers;

use App\Models\Advantage;
use App\Models\AdvantageAutomation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class AdvantageFormController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"AdvantageForms");
        try {
            $automations = AdvantageAutomation::query()->with(["employee","current_role","user"])
                ->whereHas("user",function ($query){$query->where("id","=",Auth::id());})->orderBy("id","desc")->get();
            $advantages = Advantage::query()->with("attachments")->where("inactive","=",0)->get();
            $employees = Employee::query()->with("contract")->where("unemployed","=",0)->get();
            return view("staff.advantage_forms",["advantages" => $advantages,"employees" => $employees,"automations" => $automations]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"AdvantageForms");
        try {
            $request->validate(["employee_id" => "required","advantage_id" => "required"],[
               "employee_id.required" => "انتخاب پرسنل الزامی می باشد","advantage_id.required" => "انتخاب فرم تغییرات مزایا الزامی می باشد"
            ]);
            $employee = Employee::query()->with("contract")->findOrFail($request->input("employee_id"));
            $advantage = Advantage::query()->with("attachments")->findOrFail($request->input("advantage_id"));
            return view("staff.new_advantage_form",["employee" => $employee,"advantage" => $advantage,"months" => $this->month_names()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"AdvantageForms");
        try {
            DB::beginTransaction();
            $employee = Employee::query()->with("contract")->findOrFail($request->input("employee_id"));
            $advantage = Advantage::query()->with("attachments")->findOrFail($request->input("advantage_id"));
            $automation =AdvantageAutomation::query()->create([
                "role_id" => Auth::user()->role->id,
                "user_id" => Auth::id(),
                "employee_id" => $employee->id,
                "advantage_id" => $advantage->id,
                "advantage_form" => $advantage->name,
                "type" => $request->type,
                "contract" => $employee->contract->workplace,
                "role_priority" => 1
            ]);
            $texts = [];$files = [];
            foreach (json_decode($advantage->attachments->texts,true) as $text) {
                $slug = Str::replace(" ", "_", $text);
                $request->filled($slug) ? $texts[] = ["name" => $text, "value" => $request->input($slug)] : "";
            }
            foreach (json_decode($advantage->attachments->files,true) as $file) {
                $slug = Str::replace(" ", "_", $file);
                if ($request->hasFile($slug)) {
                    $upload_files = [];
                    foreach ($request->file($slug) as $upload_file) {
                        $filename = $upload_file->hashName();
                        $upload_files[] = $filename;
                        Storage::disk('advantage_files')->put($automation->id, $upload_file);
                    }
                    $files[] = ["name" => $file, "value" => $upload_files];
                }
            }
            $automation->update([
                "texts" => json_encode($texts,JSON_UNESCAPED_UNICODE),
                "files" => json_encode($files,JSON_UNESCAPED_UNICODE),
                "start_month" => $request->input("start_month"),
                "end_month" => $request->input("end_month"),
            ]);
            DB::commit();
            return redirect()->route("AdvantageForms.index")->with(["result" => "success", "message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }

    }

    public function confirm($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('confirm',"AdvantageForms");
        try {
            DB::beginTransaction();
            $automation = AdvantageAutomation::query()->with("advantage.automation_flow.details")->findOrFail($id);
            $automate = $automation->automate("forward");
            if($automate)
                $this->send_notification($automate["users"],$automate["data"]);
            $automation->signs()->updateOrCreate(["signable_id" => $automation->id,"user_id" => Auth::id()],[
                "user_id" => Auth::id(),
                "sign" => Auth::user()->sign
            ]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "confirmed"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"AdvantageForms");
        try {
            $pathToFile = Storage::disk('advantage_files')->path("{$id}/K4B0mIFFMgMWmjZlx999lPlUZFmRn85Kzy3WjtiC.xlsx");
            $automation = AdvantageAutomation::query()->with(["employee","advantage"])->findOrFail($id);
            return view("staff.edit_advantage_form",["automation" => $automation,"months" => $this->month_names(),"files" => $pathToFile]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('edit',"AdvantageForms");
        try {
//            DB::beginTransaction();
//            $automation = AdvantageAutomation::query()->findOrFail($id);
//            $advantage = Advantage::query()->with("attachments")->findOrFail($automation->advantage_id);
//            $automation->update([
//                "role_id" => Auth::user()->role->id,
//                "texts" => null,
//                "user_id" => Auth::id(),
//                "type" => $request->type,
//                "start_month" => $request->input("start_month"),
//                "end_month" => $request->input("end_month"),
//            ]);
//            $texts = [];$files = json_decode($automation->files,true);
//            foreach (json_decode($advantage->attachments->texts,true) as $text) {
//                $slug = Str::replace(" ", "_", $text);
//                $request->filled($slug) ? $texts[] = ["name" => $text, "value" => $request->input($slug)] : "";
//            }
//            foreach (json_decode($advantage->attachments->files,true) as $file) {
//                $slug = Str::replace(" ", "_", $file);
//                if ($request->hasFile($slug)) {
//                    if ($files) {
//                        $index = array_search($file, array_column($files, "name"));
//                        if ($index >= 0) {
//                            foreach ($files[$index]["value"] as $old_file)
//                                Storage::disk("advantage_files")->delete("{$id}/{$old_file}");
//                            unset($files[$index]);
//                            $files = array_values($files);
//                        }
//                    }
//                    $upload_files = [];
//                    foreach ($request->file($slug) as $upload_file) {
//                        $filename = $upload_file->hashName();
//                        $upload_files[] = $filename;
//                        Storage::disk('advantage_files')->put($automation->id, $upload_file);
//                    }
//                    $files[] = ["name" => $file, "value" => $upload_files];
//                }
//            }
//            $automation->update([
//                "texts" => json_encode($texts,JSON_UNESCAPED_UNICODE),
//                "files" => json_encode($files,JSON_UNESCAPED_UNICODE),
//            ]);
//            DB::commit();
//            return redirect()->back()->with(["result" => "success", "message" => "saved"]);
            return redirect()->back()->withErrors(["result" => "در حال حاضر امکان ویرایش فرم تغییرات مزایا وجود ندارد"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"AdvantageForms");
        try {
            DB::beginTransaction();
            $automation = AdvantageAutomation::query()->findOrFail($id);
            Storage::disk("advantage_files")->deleteDirectory($id);
            $automation->delete();
            DB::commit();
            return redirect()->route("AdvantageForms.index")->with(["result" => "success", "message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function attached_files($id,$slug): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
            $zip = new ZipArchive();
            $zip_filename = "advantages_" . time() . rand(142,987) . ".zip";
            if(!Storage::disk("advantage_files")->exists("zip")) Storage::disk("advantage_files")->makeDirectory("zip");
            $zip->open(Storage::disk("advantage_files")->path("zip/{$zip_filename}"), ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $advantage_automation = AdvantageAutomation::query()->findOrFail($id);
            $files = json_decode($advantage_automation->files,true);
            $index = array_search($slug,array_column($files,"name"));
            if ($index >= 0){
                foreach ($files[$index]["value"] as $file){
                    $path = Storage::disk("advantage_files")->path("{$id}/{$file}");
                    $filename = basename($path);
                    $zip->addFile($path,$filename);
                }
                $zip->close();
                return response()->download(Storage::disk("advantage_files")->path("zip/{$zip_filename}"));
            }
            else
                return redirect()->back()->withErrors(["result" => "فایل(های) برای اطلاعات انتخاب شده وجود ندارند"]);
    }
    public function attached_file($id,$filename): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        if (Storage::disk("advantage_files")->exists("{$id}/{$filename}"))
            return response()->download(Storage::disk("advantage_files")->path("{$id}/{$filename}"));
        else
            return redirect()->back()->withErrors(["result" => "فایل مورد نظر جهت دانلود یافت نشد"]);
    }
}
