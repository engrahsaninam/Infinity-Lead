<table class="table2excel">
    <thead>
    <tr>
        @foreach ($campaign->list->csv->headers as $head)
        <th>
            {{ $head }}
        </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr>
            @for ($x = 1; $x <= $campaign->list->csv->columns; $x++)
                <td>{{ $row->subscriber->{'column_' . $x} }}</td>
            @endfor
        </tr>
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


        const rawSheetName = "{{$campaign->name}} - {{str_replace('_', ' ', $filter)}}".replace(/[\[\]\*\/\\\?\:]/g, '').substring(0, 20)+".xlsx";
        XLSX.utils.book_append_sheet(wb, worksheet, rawSheetName);

        XLSX.writeFile(wb, rawSheetName);
        setTimeout(() => window.close(), 500);
    }
    document.addEventListener("DOMContentLoaded", exportToExcel);
</script>