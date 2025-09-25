<table class="table2excel">
    <thead class="bg-info text-light">
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Full Name</th>
        <th>Designation</th>
        <th>Email</th>
        <th>Status</th>
        <th>Company</th>
        <th>Linkedin Profile</th>
        <th>Region</th>
    </tr>
    </thead>
    <tbody>

    <tbody>
    @foreach($lead->records as $item)
        @php
            $emails = $item->emails;
        @endphp
        @if($emails->isEmpty())
            @if($type === 'skipped' || $type === 'all')
                <tr>
                    <td>{{ $item['first_name'] }}</td>
                    <td>{{ $item['last_name'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>--</td> {{-- No email --}}
                    <td>Skipped</td>
                    <td>{{ $item['company'] }}</td>
                    <td>{{ $item['url'] }}</td>
                    <td>{{ $item['region'] }}</td>
                </tr>
            @endif
        @else
            @foreach($emails as $generated)
                @php
                    $status = strtolower($generated->status);
                    if (!in_array($status, ['valid', 'invalid'])) {
                        $status = 'guessed';
                    }
                @endphp

                @if(
                    $type === 'all' ||
                    ($type === 'valid' && $status === 'valid') ||
                    ($type === 'invalid' && $status === 'invalid') ||
                    ($type === 'guessed' && $status === 'guessed')
                )
                    <tr>
                        <td>{{ $item['first_name'] }}</td>
                        <td>{{ $item['last_name'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ $generated->email }}</td>
                        <td>{{ ucfirst($status) }}</td>
                        <td>{{ $item['company'] }}</td>
                        <td>{{ $item['url'] }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        const table = document.querySelector(".table2excel");
        const rows = Array.from(table.rows).map(row =>
            Array.from(row.cells).map(cell => cell.innerText)
        );
        const worksheet = {};
        const range = { s: { c: 0, r: 0 }, e: { c: 0, r: 0 } };
        rows.forEach((row, rowIndex) => {
            row.forEach((cell, colIndex) => {
                const cellRef = XLSX.utils.encode_cell({ r: rowIndex, c: colIndex });
                worksheet[cellRef] = {
                    v: cell,
                    t: 's'
                };
                if (range.e.c < colIndex) range.e.c = colIndex;
                if (range.e.r < rowIndex) range.e.r = rowIndex;
            });
        });
        worksheet['!ref'] = XLSX.utils.encode_range(range);
        const wb = XLSX.utils.book_new();


        const rawSheetName = "{{$lead->name}}".replace(/[\[\]\*\/\\\?\:]/g, '').substring(0, 20)+".xlsx";
        XLSX.utils.book_append_sheet(wb, worksheet, rawSheetName);

        XLSX.writeFile(wb, rawSheetName);
        setTimeout(() => window.close(), 500);
    }
    document.addEventListener("DOMContentLoaded", exportToExcel);
</script>