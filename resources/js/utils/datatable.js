import DataTable from 'datatables.net';
import 'datatables.net-responsive';

export function createDataTable(selector, options) {
    return new DataTable(selector, {
        processing: true,
        serverSide: true,
        responsive: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']],
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
        language: {
            processing: '<span class="loading loading-spinner loading-sm text-blue-600"></span> Memuat data...',
            search: '',
            searchPlaceholder: 'Cari data...',
            lengthMenu: 'Tampilkan _MENU_',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Belum ada data',
            infoFiltered: '(disaring dari _MAX_ data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data',
            paginate: { first: 'Pertama', previous: '‹', next: '›', last: 'Terakhir' },
        },
        ...options,
    });
}
