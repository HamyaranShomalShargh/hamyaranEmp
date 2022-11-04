<table style="direction: rtl">
    <thead>
    <tr>
        <th style="color: #ffffff;background-color: #343A40">ردیف</th>
        <th style="color: #ffffff;background-color: #343A40">نام</th>
        <th style="color: #ffffff;background-color: #343A40">کد ملی</th>
        @if($extra)
            <th style="color: #ffffff;background-color: #343A40">گروه شغلی</th>
            <th style="color: #ffffff;background-color: #343A40">دستمزد روزانه</th>
        @endif
        @forelse($contract->performance_attribute->items as $attribute)
            <th style="color: #ffffff;background-color: #343A40">{{ $attribute->name }}</th>
        @empty
        @endforelse
    </tr>
    </thead>
    <tbody>
    @if($contract->performance_automation)
        @forelse($contract->performance_automation->performances as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->employee->first_name." ".$item->employee->last_name }}</td>
                <td>{{ $item->employee->national_code }}</td>
                @if($extra)
                    <td>{{ $item->job_group }}</td>
                    <td>{{ $item->daily_wage }}</td>
                @endif
                @forelse($contract->performance_attribute->items as $attribute)
                    <td>{{ $item->employee["performance_data"][$loop->index] }}</td>
                @empty
                @endforelse
            </tr>
        @empty
        @endforelse
    @else
        @forelse($contract->employees as $employee)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $employee->first_name." ".$employee->last_name }}</td>
                <td>{{ $employee->national_code }}</td>
                @if($extra)
                    <td>{{ $employee->job_group }}</td>
                    <td>{{ $employee->daily_wage }}</td>
                @endif
            </tr>
        @empty
        @endforelse
    @endif
    </tbody>
</table>
