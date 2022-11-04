<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Events\PerformanceAutomationEvent;
use App\Models\InvoiceCoverTitleItem;
use App\Models\TableAttributeItem;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use nusoap_client;
use Throwable;
use ZipArchive;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function month_names(): array
    {
        return ["فروردین","اردیبهشت","خرداد","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند"];
    }

    public function activation($model): string
    {
        if ($model->inactive == 1)
            $model->update(["inactive" => 0]);
        else
            $model->update(["inactive" => 1]);
        return match($model->inactive){
            0 => "active",
            1 => "inactive",
            default => "unknown"
        };
    }
    #[ArrayShape(["success" => "int", "message" => "string"])] public function download($folder, $disk, $folder_type): array
    {
        try {
            $zip = new ZipArchive();
            if (!Storage::disk($disk)->exists("/zip/{$folder}"))
                Storage::disk($disk)->makeDirectory("/zip/{$folder}");
            if ($zip->open(Storage::disk($disk)->path("/zip/{$folder}/docs.zip"), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = File::files(storage_path("/app/$folder_type/$disk/{$folder}"));
                foreach ($files as $file)
                    $zip->addFile($file, basename($file));
                $zip->close();
                return ["success" => 1,"message" => "ready"];
            }
            else
                return ["success" => 0,"message" => "عدم توانایی در ساخت فایل فشرده"];
        }
        catch (Throwable $error){
            return ["success" => 0,"message" => $error->getMessage()];
        }
    }
    public function send_sms(array $mobile_numbers,string $text){
        try {
            $text .= "\n\r".env('APP_PERSIAN_NAME');
            $client = new nusoap_client(env("SMS_WSDL_LINK"), true);
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = false;
            if ($client->getError())
                throw new Exception('خطا در اتصال به سامانه پیامکی',$client->getError());
            $sent = $client->call('sendSMS',
                [
                    'domain' => env('SMS_DOMAIN'),
                    'username' => env('SMS_WSDL_USERNAME'),
                    'password' => env('SMS_WSDL_PASSWORD'),
                    'from' => env('SMS_FROM_NUMBER'),
                    "to" => implode(";",$mobile_numbers),
                    "text" => $text,
                    "isflash" => "1"
                ]
            );
            return $client->call('getDelivery', array('domain' => env('SMS_DOMAIN'),'username' => env('SMS_WSDL_USERNAME'),'password' => env('SMS_WSDL_PASSWORD'), 'id' => $sent));
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function send_notification($users,$data){
        if (count($users) > 0){
            foreach ($users as $user)
                event(new NotificationEvent($user->id,$data));
        }
    }

    public function default_invoice_cover_titles(): array
    {
        return [
            new InvoiceCoverTitleItem(["name" =>"هزینه ناخالص پرسنل","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "سهم 23 درصد بیمه کارفرما","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "پلوس مدیریت پیمان پیمان کار","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "کسر 10 درصد حسن انجام کار از مدیریت پیمان پیمانکار","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "کسر 5 درصد سپرده بیمه ماده 38 از مدیریت پیمان پیمانکار","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "مالیات بر ارزش افزوده","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "بیمه تکمیلی سهم کارفرما","kind" => "number","is_operable" => 0]),
            new InvoiceCoverTitleItem(["name" => "مزایای غیر نقد","kind" => "number","is_operable" => 0]),
        ];
    }
    public function default_performance_attributes(): array
    {
        return [
            new TableAttributeItem(["name" =>"روزهای کارکرد","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "اضافه کار","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "م.ساعتی","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "ماموریت روزانه","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "شب کاری","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "جمعه کاری","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "تعطیل کاری","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "نوبت کاری","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "ایاب ذهاب","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "حق غذا","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "م.استحقاقی","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "م.استعلاجی","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "م.بدون حقوق","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "جمع کسورات","kind" => "number","is_operable" => 0]),
            new TableAttributeItem(["name" => "توضیحات","kind" => "text","is_operable" => 0])
        ];
    }
    public function default_invoice_attributes(): array
    {
        $functions = ["کارکرد","اضافه کاری","اضافه کاری معوقه",
            "اضافه کار آموزشی","تعطیل کاری","ساعت کار شب","جمعه کاری",
            "تعداد اولاد","تعداد روز ماموریت"];
        $advantages = ["دستمزد روزانه","مزد شغل","مزد پایه سنوات روزانه","حقوق ماهیانه","مبلغ اضافه کاری","مبلغ اضافه کاری معوقه","مبلغ اضافه کار آموزشی",
            "مبلغ تعطیل کاری","مبلغ کار شب","مبلغ جمعه کاری","مسکن","بن","اولاد","کمک هزینه نگهداری فرزند","معوقه کمک هزینه نگهداری فرزند",
            "حق تلفن همراه","معوقه حق تلفن همراه","کارپردازی","مبلغ ماموریت","حق فرزند معلول","فوق العاده ایثارگری",
            "ایاب ذهاب","معوقه ایاب ذهاب","حق مسئولیت","معوقه حق مسئولیت","معوقه",
            "مزایا غیر نقد ازدواج","مزایا غیر نقد تولد","مزایا غیر نقد تولد فرزند","مزایا غیر نقد سفر","مزایا غیر نقد بوم گردی","مزایا غیر نقد دانش آموز ممتاز",
            "مزایا غیر نقد","مزایا غیر نقد معوق",];
        $deductions = [
            "بیمه تکمیل درمان سهم کارمند","معوقه بیمه تکمیل درمان سهم کارمند",
            "کسورات صندوق","کسورات وام دوم صندوق","کسورات وام سوم صندوق","معوقه کسورات صندوق","حق عضویت","پس انداز قرض الحسنه شهرداری","خدمات بیمه ایی",
            "همیار حامی","معوقه خدمات بیمه ای همیار حامی","خدمات بیمه ایی","فرهیختگان","بیمه حامیان شهر توس","کسورات بازگشت به شهرداری","قوه قضاییه - بانک (همیاران)",
            "قسط مساعده از شرکت و خرید دوچرخه","بیمه ماشین","معوقه بیمه ماشین","مساعده از سازمان","صندوق رفاه سازمان","کسورات بیمه عمر", "دادگاه و بانک (سازمان)"
        ];
        $items = [];
        foreach ($functions as $function)
            $items[] = new TableAttributeItem(["name" => $function, "category" => "function", "kind" => "number", "is_operable" => 0]);
        foreach ($advantages as $advantage)
            $items[] = new TableAttributeItem(["name" => $advantage, "category" => "advantage", "kind" => "number", "is_operable" => 0]);
        foreach ($deductions as $deduction)
            $items[] = new TableAttributeItem(["name" => $deduction, "category" => "deduction", "kind" => "number", "is_operable" => 0]);

        return $items;
    }
}
