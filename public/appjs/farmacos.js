$(function () {

    $("#example2").DataTable({
        "responsive": true, 
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                extend: 'copy',
                text: '<img src="dist/img/icons/copiar-80.png" class="icono"></img>',
                titleAttr: 'Copiar al portapapeles'
                
            },
            {
                extend: 'csv',
                text: '<img src="dist/img/icons/csv-80.png" class="icono"></img>',
                titleAttr: 'Exportar a un archivo csv'
                
            }, 
            {
                extend: 'excel',
                text: '<img src="dist/img/icons/xls-80.png" class="icono"></img>',
                titleAttr: 'Exportar a hoja de Excel'
                
            }, 
            {
                extend: 'pdf',
                text: '<img src="dist/img/icons/pdf-80.png" class="icono"></img>',
                titleAttr: 'Exportar en formato pdf'
                
            }, 
            {
                extend: 'print',
                text: '<img src="dist/img/icons/imprimir-40.png" class="icono"></img>',
                titleAttr: 'Imprimir todos los datos'
                
            }, 
            "colvis"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

    
});


/* 
document.addEventListener("DOMContentLoaded", function() {
const list = new List('table-default', {
    sortClass: 'table-sort',
    listClass: 'table-tbody',
    valueNames: [ 'sort-name', 'sort-type', 'sort-city', 'sort-score',
        { attr: 'data-date', name: 'sort-date' },
        { attr: 'data-progress', name: 'sort-progress' },
        'sort-quantity'
    ],
    paginate: true
});


});

*/


