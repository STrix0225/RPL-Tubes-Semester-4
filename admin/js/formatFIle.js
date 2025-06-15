
$(document).ready(function () {
    const table = $('#restockTable').DataTable({
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 4 },
            { responsivePriority: 3, targets: 11 },
            { responsivePriority: 4, targets: 13 }
        ]
    });

    // Utility: Format date as YYYY-MM-DD
    function formatDate(date) {
        return date.toISOString().slice(0, 10);
    }

    // Utility: Remove currency symbols and formatting
    function cleanCurrency(value) {
        return value.replace(/[^0-9.,]/g, '');
    }

    // ---------------- CSV EXPORT ----------------
    $('#exportCSV').click(function () {
        const data = table.rows({ search: 'applied' }).data().toArray();

        const headers = [
            'No', 'ID', 'Date', 'Supplier', 'Product', 'Brand',
            'Category', 'Color', 'Qty', 'Unit Price', 'Total Price',
            'Status', 'Notes'
        ];

        // CSV header
        let csvContent = '"' + headers.join('","') + '"\n';

        // CSV rows
        // CSV rows
data.forEach((row, index) => {
    const rowData = [
        index + 1,
        row[1],
        row[2],
        row[3],
        row[4],
        row[5],
        row[6],
        row[7],
        row[8],
        cleanCurrency(row[9]),
        cleanCurrency(row[10]),
        String(row[11]).replace(/<[^>]*>/g, '').trim(),
        row[12] ? row[12].replace(/"/g, '""') : ''
    ];

    csvContent += '"' + rowData.join('","') + '"\n';
});


        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'restock_history_' + formatDate(new Date()) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // ---------------- PDF EXPORT ----------------
    $('#exportPDF').click(function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        const data = table.rows({ search: 'applied' }).data().toArray();

        // Title
        doc.setFontSize(16);
        doc.text('Restock History Report', 14, 15);

        // Date
        doc.setFontSize(10);
        doc.text('Generated on: ' + new Date().toLocaleDateString('id-ID'), 14, 22);

        // Headers
        const headers = [[
            'No', 'ID', 'Date', 'Supplier', 'Product', 'Brand',
            'Category', 'Color', 'Qty', 'Unit Price', 'Total Price',
            'Status', 'Notes'
        ]];

        // Body
        const pdfData = data.map((row, index) => ([
            index + 1,
            row[1],
            row[2],
            row[3],
            row[4],
            row[5],
            row[6],
            row[7],
            row[8],
            cleanCurrency(row[9]),
            cleanCurrency(row[10]),
            row[11].replace(/<[^>]*>/g, ''),
            row[12] || '-'
        ]));

        // Table
        doc.autoTable({
            head: headers,
            body: pdfData,
            startY: 30,
            margin: { left: 10, right: 10 },
            styles: {
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle'
            },
            columnStyles: {
                0: { cellWidth: 10 },   // No
                1: { cellWidth: 15 },   // ID
                2: { cellWidth: 20 },   // Date
                3: { cellWidth: 30 },   // Supplier
                4: { cellWidth: 30 },   // Product
                5: { cellWidth: 20 },   // Brand
                6: { cellWidth: 20 },   // Category
                7: { cellWidth: 15 },   // Color
                8: { cellWidth: 10 },   // Qty
                9: { cellWidth: 18 },   // Unit Price
                10: { cellWidth: 18 },  // Total Price
                11: { cellWidth: 18 },  // Status
                12: { cellWidth: 40 }   // Notes
            },
            headStyles: {
                fillColor: [44, 62, 80], // dark blue
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            didDrawPage: function (data) {
                doc.setFontSize(10);
                doc.setTextColor(150);
                doc.text(
                    'Page ' + doc.internal.getNumberOfPages(),
                    doc.internal.pageSize.width - 10,
                    doc.internal.pageSize.height - 10,
                    { align: 'right' }
                );
            }
        });

        doc.save('restock_history_' + formatDate(new Date()) + '.pdf');
    });
});
