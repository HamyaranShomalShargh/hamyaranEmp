<table style="direction: rtl">
    <thead>
    <tr>
        <th style="color: #ffffff;background-color: #343A40">ردیف</th>
        <th style="color: #ffffff;background-color: #343A40">نام</th>
        <th style="color: #ffffff;background-color: #343A40">کد ملی</th>
        <th style="color: #ffffff;background-color: #343A40">گروه شغلی</th>
        @if($type == "created")
            @forelse($contract->invoice_automation->attributes->items as $attribute)
                <th style="color: #ffffff;background-color: #343A40">{{ $attribute->name }}</th>
            @empty
            @endforelse
        @else
            @forelse($automation->contract->invoice_attribute->items as $attribute)
                <th style="color: #ffffff;background-color: #343A40">{{ $attribute->name }}</th>
            @empty
            @endforelse
        @endif
    </tr>
    </thead>
    <tbody>
    @if($type == "created")
        @forelse($contract->invoice_automation->invoices as $invoice)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $invoice->employee->first_name." ".$invoice->employee->last_name }}</td>
                <td>{{ $invoice->employee->national_code }}</td>
                <td>{{ $invoice->job_group }}</td>
                @forelse($contract->invoice_automation->attributes->items as $attribute)
                    <td>{{ $invoice->employee["invoice_data"][$loop->index] }}</td>
                @empty
                @endforelse
            </tr>
        @empty
        @endforelse
    @else
        @forelse($automation->performances as $performance)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $performance->employee->first_name." ".$performance->employee->last_name }}</td>
                <td>{{ $performance->employee->national_code }}</td>
                <td>{{ $performance->job_group }}</td>
            </tr>
        @empty
        @endforelse
    @endif
    </tbody>
</table>
